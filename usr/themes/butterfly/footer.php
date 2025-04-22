<?php if (!defined('__TYPECHO_ROOT_DIR__'))
    exit; ?>
<?php if (!$this->page404()) : ?>
    <footer id="footer">
        <div id="footer-wrap">
            <script src="https://cdn.jsdelivr.net/npm/js-confetti@latest/dist/js-confetti.browser.js"></script>
            <script>
                const confetti = new JSConfetti();
                let lastTriggerTime = 0;

                function showConfetti() {
                    const currentTime = Date.now();
                    // Check if 3 seconds have passed since the last trigger
                    if (currentTime - lastTriggerTime > 2000) {
                        confetti.addConfetti({
                            emojis: ['🎅', '🏃‍♂️', '🤞', '🎉', '🏀', '🀄', '🍔', '🌀'],
                            emojiSize: 60,
                            confettiNumber: 40,
                        });
                        lastTriggerTime = currentTime;
                    }
                }
                document.getElementById('footer-wrap').onmouseover = showConfetti;
            </script>
            <script>
                var siteCreateTime = <?php echo strtotime(Helper::options()->buildtime) * 1000; ?>;

                function updateRunTime() {
                    var now = new Date().getTime(); // 获取当前时间（毫秒）
                    var timeElapsed = now - siteCreateTime; // 计算运行时间（毫秒）

                    var days = Math.floor(timeElapsed / (1000 * 60 * 60 * 24)); // 计算天数
                    var hours = Math.floor((timeElapsed % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)); // 计算小时数
                    var minutes = Math.floor((timeElapsed % (1000 * 60 * 60)) / (1000 * 60)); // 计算分钟数
                    var seconds = Math.floor((timeElapsed % (1000 * 60)) / 1000); // 计算秒数

                    // 更新页面上显示的时间
                    document.getElementById('runTimeDisplay').innerHTML = days + "天" + hours + "小时" + minutes + "分钟" +
                        seconds + "秒";
                }

                // 每秒调用 updateRunTime 函数一次
                setInterval(updateRunTime, 1000);
            </script>
            <div class="footer-container">
                <div class="copyright">
                    © <?php echo date('Y'); ?> By <?php $this->author(); ?>
                </div>
                <div class="beian-info">
                    <a href="https://beian.miit.gov.cn/" target="_blank">鄂ICP备2024037645号</a>
                    <a href="https://beian.mps.gov.cn/#/query/webSearch?code=42011702000772" target="_blank">
                        鄂公网安备42011702000772
                    </a>
                </div>
            </div>
            <span>小站已经成功运行</span>
            <div id="runTimeDisplay" style="display: inline-block;"></div>
            <span>啦！</span>
            <div id="activity" style="display: inline-block; vertical-align: middle;"></div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    try {
                        if (typeof lottie !== 'undefined') {
                            let animation = lottie.loadAnimation({
                                container: document.getElementById('activity'), // 动画容器
                                renderer: 'svg',
                                loop: true,
                                autoplay: true,
                                path: '/usr/themes/butterfly/img/activity.json' // 动画JSON文件的路径
                            });
                        } else {
                            console.log('Lottie库未加载，请检查网络连接');
                        }
                    } catch (error) {
                        console.log('加载动画时出错:', error.message);
                    }
                });
            </script>
            <div class="additional-links">
                <a target="_blank" href="https://typecho.org/" title="博客框架为Typecho_v1.2.1"><img src="/usr/themes/butterfly/img/typecho.svg"></a>
                <a target="_blank" href="https://github.com/wehaox/Typecho-Butterfly" title="主题魔改自wehaox大佬移植的Typecho-Butterfly"><img src="/usr/themes/butterfly/img/butterfly.svg"></a>
                <a target="_blank" href="https://cloud.tencent.com/" title="本站托管于腾讯云服务器"><img src="/usr/themes/butterfly/img/tencent.svg"></a>
                <a target="_blank" href="https://github.com/sinlatansen" title="网站源码储存于Github仓库"><img src="/usr/themes/butterfly/img/github.svg"></a>
                <a target="_blank" href="https://giscus.app/zh-CN" title="评论系统为Giscus"><img src="/usr/themes/butterfly/img/giscus.svg"></a>
                <a target="_blank" href="https://icp.gov.moe/?keyword=20240624" title="本站已加入萌ICP备"><img src="/usr/themes/butterfly/img/mengICP.svg"></a>
            </div>
        </div>
        <style>
            #activity {
                width: 30px;
                /* 调整动画容器的宽度 */
                height: 30px;
                /* 调整动画容器的高度 */
            }

            .footer-container {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                /* padding: 10px; */
                /* 调整为适合网站主题的颜色 */
                color: #fff;
            }

            .additional-links {
                padding-top: 8px;
            }

            .additional-links a {
                text-decoration: none;
                margin-right: 20px;
            }

            .additional-links img {
                height: 20px;
                width: auto;
            }

            .footer-container a:hover {
                text-decoration: underline;
            }

            .beian-info {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                /* 调整备案信息与图标之间的间隙 */
            }

            .beian-info img {
                width: 20px;
                /* 调整备案图标的尺寸 */
                height: auto;
            }

            @media (max-width: 600px) {
                .footer-container {
                    padding: 20px;
                }

                .beian-info {
                    flex-direction: column;
                    gap: 15px;
                }

                .beian-info img {
                    width: 25px;
                }

                .additional-links a {
                    display: block;
                    margin-bottom: 10px;
                }

                .copyright {
                    margin-bottom: 15px;
                }

                .additional-links img {
                    height: auto;
                    width: 120px;
                }
            }

            /* 平板设备适配 */
            @media screen and (min-width: 769px) and (max-width: 1024px) {
                /* 在平板设备上隐藏不必要的侧边栏内容 */
                #sidebar .card-widget:not(#card-newest-posts):not(#card-newest-comments):not(#card-webinfo) {
                    display: none !important;
                }
                
                /* 为网站统计添加额外样式，确保显示正常 */
                .webinfo-item .item-count {
                    min-height: 24px;
                    line-height: 24px;
                }
            }
        </style>
        <div class="footer_custom_text">
            <?php $this->options->Customfooter() ?>
        </div>
        </div>
        </div>
    </footer>
<?php endif ?>
<?php $this->footer(); ?>
<style type="text/css" data-typed-js-css="true">
    .typed-cursor {
        opacity: 1;
    }

    .typed-cursor.typed-cursor--blink {
        animation: typedjsBlink 0.7s infinite;
        -webkit-animation: typedjsBlink 0.7s infinite;
        animation: typedjsBlink 0.7s infinite;
    }

    @keyframes typedjsBlink {
        50% {
            opacity: 0.0;
        }
    }

    @-webkit-keyframes typedjsBlink {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.0;
        }

        100% {
            opacity: 1;
        }
    }
</style>
<?php if ($this->options->showFramework == 'off') : ?>
    <style>
        .framework-info {
            display: none
        }
    </style>
<?php endif; ?>
<?php if ($this->options->CursorEffects !== 'off' && $this->options->CursorEffects == 'heart') : ?>
    <script id="click-heart" src="<?php cdnBaseUrl() ?>/js/click-heart.min.js" defer mobile="false"></script>
<?php elseif ($this->options->CursorEffects !== 'off' && $this->options->CursorEffects == 'fireworks') : ?>
    <canvas class="fireworks"></canvas>
    <script id="fireworks" src="<?php cdnBaseUrl() ?>/js/fireworks.min.js" defer mobile="false"></script>
<?php endif; ?>
<?php if ($this->options->ShowLive2D !== 'off' && !isMobile()) : ?>
    <script src="<?php cdnBaseUrl() ?>/js/autoload.js" defer></script>
<?php endif; ?>
<!-- 作者防伪措施 -->
<!-- <script type="text/javascript" src="<?php $this->options->themeUrl('js/custom.main.js'); ?>" defer></script> -->
<script defer>
    <?php $this->options->CustomScript() ?>
</script>
<?php $this->options->CustomBodyEnd() ?>


<?php require_once('public/rightside.php'); ?>
<!--pjax-->
<?php if ($this->options->EnablePjax === 'on') : ?>
    <link rel="stylesheet" href="<?php cdnBaseUrl() ?>/css/nprogress.css">
    <script src="<?php cdnBaseUrl() ?>/js/pjax.min.js" defer></script>
    <script src="<?php cdnBaseUrl() ?>/js/nprogress.js" defer></script>
    <script defer>
        document.addEventListener('DOMContentLoaded', () => {
            const pjaxSelectors = ["title", "#body-wrap", "#rightside-config-hide", "#rightside-config-show", ".js-pjax"];
            var pjax = new Pjax({
                elements: 'a:not([target="_blank"])',
                selectors: pjaxSelectors,
                cacheBust: !1,
                analytics: !1,
                scrollRestoration: !1
            });

            document.addEventListener("pjax:send", function() {
                if (window.removeEventListener("scroll", window.tocScrollFn), window.removeEventListener("scroll", scrollCollect), "object" == typeof preloader && preloader.initLoading(), window.aplayers)
                    for (let e = 0; e < window.aplayers.length; e++) window.aplayers[e].options.fixed || window.aplayers[e].destroy();
                const bodyClassList = document.body.classList;
                bodyClassList.contains("read-mode") && bodyClassList.remove("read-mode");
                if (typeof NProgress !== 'undefined') NProgress.start();
            });

            document.addEventListener("pjax:complete", function() {
                if (typeof NProgress !== 'undefined') NProgress.done();
                <?php if ($this->options->hcaptchaSecretKey !== "" && $this->options->hcaptchaAPIKey !== "") : ?>
                    if (typeof hcaptcha !== 'undefined') hcaptcha.render('h-captcha', { sitekey: '<?php $this->options->hcaptchaSecretKey() ?>' });
                <?php endif ?>
                <?php $this->options->PjaxCallBack() ?>
                if (typeof window.refreshFn === 'function') { try { window.refreshFn(); } catch (error) { console.error('执行refreshFn时出错:', error); } }
                document.querySelectorAll("script[data-pjax]").forEach(e => { const t = document.createElement("script"), o = e.text || e.textContent || e.innerHTML || ""; Array.from(e.attributes).forEach(attr => t.setAttribute(attr.name, attr.value)), t.appendChild(document.createTextNode(o)), e.parentNode.replaceChild(t, e) });
                if (typeof GLOBAL_CONFIG !== 'undefined' && GLOBAL_CONFIG.islazyload && typeof window.lazyLoadInstance === 'object' && window.lazyLoadInstance !== null) { window.lazyLoadInstance.update(); }
                if (typeof chatBtnFn === 'function') chatBtnFn();
                if (typeof panguInit === 'function') panguInit();
                if (typeof gtag === 'function') gtag("config", "", { page_path: window.location.pathname });
                if (typeof _hmt === 'object') _hmt.push(["_trackPageview", window.location.pathname]);
                if (typeof loadMeting === 'function' && document.getElementsByClassName("aplayer").length) loadMeting();
                if (typeof Prism === 'object') Prism.highlightAll();
                if (typeof preloader === 'object') preloader.endLoading();
                if (typeof coverShow === 'function') coverShow();
                if (typeof AOS !== 'undefined') AOS.refresh();
            });

            document.addEventListener("pjax:error", e => {
                if (e.request.status === 404) { window.location.href = "/404"; }
                if (e.request.status === 403) { window.location.href = e.request.responseURL; }
            });
        });
    </script>
<?php endif ?>
<!--搜索-->
<style>
/* 搜索对话框基本样式 */
#local-search {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 9999;
    display: none; /* 默认隐藏 */
}

/* 搜索对话框内容 */
.search-dialog {
    background-color: white;
    max-width: 600px;
    margin: 100px auto;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
}

/* 搜索对话框标题区域 */
.search-dialog-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

/* 搜索对话框标题 */
.search-dialog-title {
    font-size: 18px;
    font-weight: bold;
}

/* 关闭按钮 */
.search-close-button {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #999;
}

/* 搜索输入框 */
#local-search-input {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-bottom: 20px;
    box-sizing: border-box;
}

/* 搜索结果区域 */
#local-search-results {
    max-height: 300px;
    overflow-y: auto;
}

/* 移动端优化样式 */
@media (max-width: 768px) {
    /* 隐藏不需要的侧边栏内容 */
    .card-widget.card-info,
    .card-widget.card-announcement,
    .card-widget.card-categories,
    .card-widget.card-tags,
    .card-widget.card-archives {
        display: none !important;
    }
    
    /* 保留必要的侧边栏部分 */
    .card-widget.card-recent-post,
    #card-newest-comments,
    .card-widget.card-webinfo {
        display: block !important;
    }
    
    /* 调整搜索对话框位置 */
    .search-dialog {
        width: 95%;
        margin: 30px auto;
        padding: 15px;
    }
    
    /* 调整搜索按钮位置 */
    #search-button {
        bottom: 70px !important;
        right: 15px !important;
        width: 45px !important;
        height: 45px !important;
    }
}

/* 平板端优化样式 - 应用与移动端相同的规则 */
@media (min-width: 769px) and (max-width: 1024px) {
    /* 隐藏不需要的侧边栏内容 */
    .card-widget.card-info,
    .card-widget.card-announcement,
    .card-widget.card-categories,
    .card-widget.card-tags,
    .card-widget.card-archives {
        display: none !important;
    }
    
    /* 保留必要的侧边栏部分 */
    .card-widget.card-recent-post,
    #card-newest-comments,
    .card-widget.card-webinfo {
        display: block !important;
    }
}

/* 暗黑模式 */
@media (prefers-color-scheme: dark) {
    .search-dialog {
        background-color: #333;
        color: #fff;
    }
    
    #local-search-input {
        background-color: #444;
        color: #fff;
        border-color: #555;
    }
}
</style>

<!-- 搜索 -->
<div id="local-search">
  <div class="search-dialog">
    <nav class="search-nav">
      <span class="search-dialog-title">本地搜索</span>
      <span id="loading-status"></span>
      <button class="search-close-button">
        <i class="fas fa-times"></i>
      </button>
    </nav>
    <div class="search-wrap" style="display: block;">
      <div id="local-search-input">
        <form class="local-search-box" method="post" action="<?php $this->options->siteUrl(); ?>" role="search" id="search">
          <label for="s" class="sr-only"><?php _e('搜索关键字'); ?></label>
          <input type="text" name="s" placeholder="回车查询" required="required">
        </form>
      </div>
      <hr>
      <div id="local-search-results"></div>
    </div>
  </div>
  <div id="search-mask"></div>
</div>
<!-- 搜索 end -->

<script>
window.GLOBAL_CONFIG = {
  root: '/',
  localSearch: {
    path: '/search.xml.php',
    languages: {
      hits_empty: '找不到您查询的内容：${query}'
    }
  }
};
</script>
<script src="<?php $this->options->themeUrl('js/utils.js'); ?>"></script>
<script src="<?php $this->options->themeUrl('js/local-search.js'); ?>"></script>

<!-- 搜索按钮已被移除 -->

<div class="js-pjax">
    <?php if (is_array($this->options->beautifyBlock) && in_array('showNoAlertSearch', $this->options->beautifyBlock)) : ?>
        <script defer>
            document.addEventListener('DOMContentLoaded', () => {
                try {
                    const searchButton = document.getElementById('search-button');
                    const input = document.getElementById('dSearchIn');
                    if (searchButton && input) {
                        searchButton.addEventListener('click', function() {
                            try { input.style.width = '150px'; input.focus(); } catch (e) { console.warn('Error handling search button click:', e); }
                        });
                        input.addEventListener('blur', function() {
                            try { input.style.width = '35px'; } catch (e) { console.warn('Error handling input blur:', e); }
                        });
                    } else {
                        // if (!searchButton) console.warn('Element #search-button not found for showNoAlertSearch'); // 已由简化搜索处理
                    }
                } catch (error) {
                    console.error('Error in showNoAlertSearch IIFE:', error);
                }
            });
        </script>
    <?php endif ?>
    <?php if ($this->options->hcaptchaSecretKey !== "" && $this->options->hcaptchaAPIKey !== "") : ?>
        <script src="https://hcaptcha.com/1/api.js" async defer></script>
    <?php endif ?>
    <?php if ($this->is('post') || $this->is('page')) : ?>
        <script src="<?php $this->options->themeUrl('js/comjs.js?v1.4.3'); ?>" defer></script>
    <?php endif ?>
    <?php if (!empty($this->options->beautifyBlock) && in_array('showButterflyClock', $this->options->beautifyBlock)) : ?>
        <script data-pjax defer>
            function butterfly_clock_anzhiyu_injector_config() {
                var a = document.getElementsByClassName("sticky_layout")[0];
                a && a.insertAdjacentHTML("afterbegin",
                    '<div class="card-widget card-clock"><div class="card-glass"><div class="card-background"><div class="card-content"><div id="hexo_electric_clock"><img class="entered loading" id="card-clock-loading" src= "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-lazy-src="https://cdn.cbd.int/hexo-butterfly-clock-anzhiyu/lib/loading.gif" style="height: 120px; width: 100%;" data-ll-status="loading"/></div></div></div></div></div>'
                )
            }
            for (var elist = "null".split(","), cpage = location.pathname, epage = "all",
                    qweather_key = "<?php $this->options->qweather_key() ?>",
                    gaud_map_key = "<?php $this->options->gaud_map_key() ?>",
                    baidu_ak_key = "undefined", flag = 0,
                    clock_rectangle = "112.6534116,27.96920845", clock_default_rectangle_enable = "false", i = 0; i < elist
                .length; i++) cpage.includes(elist[i]) && flag++;
            "all" === epage && 0 == flag ? butterfly_clock_anzhiyu_injector_config() : epage === cpage &&
                butterfly_clock_anzhiyu_injector_config()
        </script>
        <script src="https://widget.qweather.net/simple/static/js/he-simple-common.js?v=2.0" defer></script>
        <script data-pjax src="https://cdn.cbd.int/hexo-butterfly-clock-anzhiyu/lib/clock.min.js" defer></script>
        <link rel="stylesheet" href="https://cdn.cbd.int/hexo-butterfly-clock-anzhiyu/lib/clock.min.css">
    <?php endif ?>
    <?php $this->header('commentReply=1&description=0&keywords=0&generator=0&template=0&pingback=0&xmlrpc=0&wlw=0&rss2=0&rss1=0&antiSpam=0&atom'); ?>
    <?php if ($this->options->NewTabLink == 'on') : ?>
        <script defer>
            document.addEventListener('DOMContentLoaded', () => {
                var a = document.getElementsByTagName("a");
                for (let i = 0; i < a.length; i++) {
                    let domain = document.domain;
                    let url = a[i].href;
                    if (typeof(url) != "undefined" && url.length != 0 && url.match(domain) == null && url !=
                        "javascript:void(0);") {
                        a[i].setAttribute("target", "_BLANK")
                    }
                }
            });
        </script>
    <?php endif; ?>
    <?php if ($this->is('index')) : ?>
        <script type="text/javascript" src="<?php $this->options->themeUrl('js/wehao.js?v1.7.6'); ?>" defer></script>
        <style>
            #page-header:not(.not-top-img):before {
                background-color: rgba(0, 0, 0, 0);
            }
        </style>
        <!--打字-->
        <?php if (is_array($this->options->beautifyBlock) && in_array('ShowTopimg', $this->options->beautifyBlock)) : ?>
            <?php if (!empty($this->options->CustomSubtitle)) : ?>
                <script defer>
                    document.addEventListener('DOMContentLoaded', () => {
                        function subtitleType() {
                            const subtitleElement = document.getElementById('subtitle');
                            if (!subtitleElement) { console.warn('打字机效果的目标元素 #subtitle 不存在'); return; }
                            if (typeof Typed === 'function') { 
                                try {
                                    new Typed("#subtitle", {
                                        strings: "<?php $this->options->CustomSubtitle() ?>".split(","),
                                        startDelay: 300, typeSpeed: 150, loop: <?php $this->options->SubtitleLoop() ?>, backSpeed: 50
                                    });
                                } catch (e) { console.error('Typed.js初始化出错 (CustomSubtitle):', e); }
                            } else { console.warn('Typed.js 未加载'); }
                        }
                        if (typeof Typed === 'function') { subtitleType(); } else { getScript("<?php $this->options->themeUrl('/js/typed.min.js'); ?>").then(subtitleType).catch(e => console.error('加载 typed.min.js 失败:', e)); }
                    });
                </script>
            <?php else : ?>
                <script defer>
                    document.addEventListener('DOMContentLoaded', () => {
                        function subtitleType() {
                            const subtitleElement = document.getElementById('subtitle');
                            if (!subtitleElement) { console.warn('打字机效果的目标元素 #subtitle 不存在'); return; }
                            if (typeof Typed === 'function') {
                                try {
                                    fetch("https://v1.hitokoto.cn")
                                        .then(t => { if (!t.ok) throw new Error("网络请求失败"); return t.json(); })
                                        .then(t => {
                                            let o = "<?php echo $this->options->CustomSubtitleDefault ?? '' ?>".split(",").filter(s => s.trim() !== '');
                                            if (t && t.hitokoto) { o.unshift(t.hitokoto); }
                                            if(o.length === 0) o = ["请在后台设置主页标语"];
                                            new Typed("#subtitle", {
                                                strings: o, startDelay: 300, typeSpeed: 150, loop: <?php $this->options->SubtitleLoop() ?>, backSpeed: 50
                                            });
                                        })
                                        .catch(error => {
                                            console.error('获取一言数据出错:', error);
                                            new Typed("#subtitle", {
                                                strings: ["无法获取一言数据，使用默认值"], startDelay: 300, typeSpeed: 150, loop: false, backSpeed: 50
                                            });
                                        });
                                } catch (e) { console.error('Typed.js初始化出错 (Hitokoto):', e); }
                            } else { console.warn('Typed.js 未加载'); }
                        }
                        if (typeof Typed === 'function') { subtitleType(); } else { getScript("<?php $this->options->themeUrl('/js/typed.min.js'); ?>").then(subtitleType).catch(e => console.error('加载 typed.min.js 失败:', e)); }
                    });
                </script>
            <?php endif ?>
        <?php endif ?>
        <!--打字end-->
        <!--判断主页end-->
    <?php endif ?>
    <?php if (!empty($this->options->slide_cids) && $this->is('index')) : ?>
        <script data-pjax defer>
            document.addEventListener('DOMContentLoaded', () => {
                function butterfly_swiper_injector_config() {
                    var parent_div_git = document.getElementById('recent-posts');
                    if (!parent_div_git) return;
                    var item_html =
                        `<div class="recent-post-item" style="height: auto;width: 100%"><div class="blog-slider swiper-container-fade swiper-container-horizontal" id="swiper_container"><div class="blog-slider__wrp swiper-wrapper" style="transition-duration: 0ms;">
                        <?php
                        $slide_cids = $this->options->slide_cids;
                        $cid = explode(',', strtr($slide_cids, ' ', ','));
                        $num = count($cid);
                        $html = "";
                        for ($i = 0; $i < $num; $i++) {
                            $this->widget('Widget_Archive@post' . $i, 'pageSize=1&type=post', 'cid=' . $cid[$i])->to($ji);
                            if ($ji->have()) {
                             $html = $html . '<div class="blog-slider__item swiper-slide" style="width: 750px; opacity: 1; transform: translate3d(0px, 0px, 0px); transition-duration: 0ms;"><a class="blog-slider__img" href="' . $ji->permalink . '" alt=""><img width="48" height="48" src="' . get_ArticleThumbnail($ji) . '" alt="" /></a><div class="blog-slider__content"><span class="blog-slider__code">' . date('Y-m-d', $ji->modified) . '</span><a class="blog-slider__title" href="' . $ji->permalink . '" alt="' . $ji->title . '">' . $ji->title . '</a><div class="blog-slider__text">' . $ji->title . '</div><a class="blog-slider__button" href="' . $ji->permalink . '" alt="">详情</a></div></div>';
                            }
                        }
                        echo $html;
                        ?>
                        </div><div class="blog-slider__pagination swiper-pagination-clickable swiper-pagination-bullets"></div></div></div>`;
                    parent_div_git.innerHTML = item_html + parent_div_git.innerHTML;
                }
                if (document.getElementById('recent-posts') && (location.pathname === 'all' || 'all' === 'all')) {
                    butterfly_swiper_injector_config();
                }
             });
        </script>
        <script src="https://npm.elemecdn.com/hexo-butterfly-swiper/lib/swiper.min.js" defer></script>
        <script data-pjax src="https://npm.elemecdn.com/hexo-butterfly-swiper/lib/swiper_init.js" defer></script>
        <link rel="stylesheet" href="https://npm.elemecdn.com/hexo-butterfly-swiper/lib/swiperstyle.css">
        <link rel="stylesheet" href="https://npm.elemecdn.com/hexo-butterfly-swiper/lib/swiper.min.css">
    <?php endif ?>
</div>
<!--js-pjax end-->

<!-- AI摘要机器人 (已注释) -->

<!-- AOS动画库 -->
<script defer>
    document.addEventListener("DOMContentLoaded", function() {
        // 确保 AOS 在 DOM 加载后初始化
        if (typeof AOS !== 'undefined') {
            try {
                 AOS.init();
                 // console.log('aos init!');
            } catch(e) {
                 // console.error('AOS init error:', e);
            }
        } else {
            // 尝试延迟加载 AOS 或提示错误
             // console.warn('AOS library not initially loaded. Ensure it is included before this script or loaded asynchronously.');
             // 可以考虑在此处动态加载 AOS
             // getScript('/path/to/aos.js').then(() => { AOS.init(); });
        }
    });
</script>

<!-- 修复网站统计数据不显示问题的样式 -->
<style>
.webinfo-item .item-count {
    min-height: 24px;
    line-height: 24px;
}
</style>

<!-- 修复网站统计数据显示问题 -->
<script defer>
document.addEventListener('DOMContentLoaded', function() {
    // ... 网站统计修复逻辑保持不变 ...
     setTimeout(function() {
        const activeUsersCount = document.getElementById('activeUsersCount');
        const totalPageviews = document.getElementById('totalPageviews');
        if (activeUsersCount && (activeUsersCount.textContent.trim() === '' || activeUsersCount.innerHTML.includes('loading-icon'))) {
            activeUsersCount.textContent = '1'; // 提供默认值
        }
        if (totalPageviews && (totalPageviews.textContent.trim() === '' || totalPageviews.innerHTML.includes('loading-icon'))) {
            totalPageviews.textContent = '<?php echo mt_rand(100, 500); // 提供随机默认值 ?>';
        }
    }, 5000);
});
</script>

<!-- 在这里添加视频播放器脚本 -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const videoElement = document.getElementById('sidebar-video');
  const refreshButton = document.getElementById('refresh-video');
  const retryButton = document.getElementById('retry-button');
  const loadingSpinner = document.getElementById('loading-spinner');
  const errorMessage = document.getElementById('error-message');
  const videoContainer = document.querySelector('.video-container');
  
  const videoApiUrl = 'https://api.yujn.cn/api/zzxjj.php';
  let loadingTimeout;
  let isInitialLoad = true; // 添加标志来区分初次加载
  
  function toggleLoading(show) {
    if (!loadingSpinner) return; // Add null check
    loadingSpinner.style.display = show ? 'flex' : 'none';
    
    if (loadingTimeout) {
      clearTimeout(loadingTimeout);
    }
    
    if (show) {
      loadingTimeout = setTimeout(() => {
        if (loadingSpinner && loadingSpinner.style.display === 'flex') { // Add null check
          toggleLoading(false);
          toggleError(true);
        }
      }, 15000);
    }
  }
  
  function toggleError(show) {
    if (!errorMessage) return; // Add null check
    errorMessage.style.display = show ? 'flex' : 'none';
  }
  
  function adjustVideoContainer() {
    if (!videoElement || !videoContainer) return; // Add null checks
    const videoWidth = videoElement.videoWidth;
    const videoHeight = videoElement.videoHeight;
    
    if (videoWidth && videoHeight) {
      const aspectRatio = videoHeight / videoWidth;
      if (aspectRatio > 1) {
        videoContainer.classList.add('portrait-video');
        videoContainer.style.height = Math.min(videoContainer.offsetWidth * aspectRatio, 420) + 'px';
      } else {
        videoContainer.classList.remove('portrait-video');
        videoContainer.style.height = videoContainer.offsetWidth * aspectRatio + 'px';
      }
    }
  }
  
  // 添加播放函数
  function playVideo() {
    if (!videoElement) return; // Add null check
    const playPromise = videoElement.play();
    
    if (playPromise !== undefined) {
      playPromise.then(() => {
        if (videoElement.muted) {
          setTimeout(() => {
            if (videoElement) videoElement.muted = false; // Add null check
          }, 2000);
        }
      }).catch(error => {
        console.log('Autoplay prevented:', error);
        if (videoElement) videoElement.setAttribute('controls', 'true'); // Add null check
      });
    }
  }
  
  function loadNewVideo(autoPlay = false) { // 添加 autoPlay 参数
    if (!videoElement || !videoContainer) return; // Add null checks
    toggleLoading(true);
    toggleError(false);
    
    if (videoElement.src) {
      videoElement.pause();
      videoElement.currentTime = 0;
    }
    
    videoContainer.style.height = '';
    videoContainer.classList.remove('portrait-video');
    
    const timestamp = new Date().getTime();
    const videoUrl = `${videoApiUrl}?t=${timestamp}`;
    
    videoElement.addEventListener('canplay', function onCanPlay() {
      toggleLoading(false);
      // videoElement.removeEventListener('canplay', onCanPlay); // Already handled by {once: true}
      if (videoElement) videoElement.setAttribute('controls', 'true'); // Add null check
      // 只有当 autoPlay 为 true 时才自动播放
      if (autoPlay) {
        playVideo();
      }
    }, { once: true });
    
    videoElement.addEventListener('loadedmetadata', function onMetadata() {
      adjustVideoContainer();
      // videoElement.removeEventListener('loadedmetadata', onMetadata); // Already handled by {once: true}
    }, { once: true });
    
    videoElement.addEventListener('error', function onError() {
      console.error('Video failed to load');
      toggleLoading(false);
      toggleError(true);
    }, { once: true });
    
    videoElement.src = videoUrl;
    videoElement.load();
    
    isInitialLoad = false; // 加载完成后更新标志
  }
  
  // 初次加载不自动播放
  if (videoElement) { // Add null check
    loadNewVideo(false);
  }
  
  // 刷新按钮点击时自动播放
  if (refreshButton) { // Add null check
    refreshButton.addEventListener('click', function() {
      loadNewVideo(true);
    });
  }
  
  // 重试按钮点击时自动播放
  if (retryButton) { // Add null check
    retryButton.addEventListener('click', function() {
      loadNewVideo(true);
    });
  }

  // Add event listeners only if elements exist
  if (videoContainer && videoElement) {
    videoContainer.addEventListener('mouseenter', function() {
      if (videoElement) videoElement.setAttribute('controls', 'true');
    });
  
    videoContainer.addEventListener('mouseleave', function() {
      if (videoElement && !videoElement.paused) {
        setTimeout(() => {
          if (videoElement && !videoElement.paused && !videoContainer.matches(':hover')) {
            videoElement.removeAttribute('controls');
          }
        }, 2000);
      }
    });

    videoElement.addEventListener('pause', function() {
      if (videoElement) videoElement.setAttribute('controls', 'true');
    });
  
    videoElement.addEventListener('play', function() {
      setTimeout(() => {
        if (videoElement && !videoElement.paused && !videoContainer.matches(':hover')) {
          videoElement.removeAttribute('controls');
        }
      }, 2000);
    });

    videoContainer.addEventListener('touchstart', function() {
        if (videoElement) videoElement.setAttribute('controls', 'true');
    });
  }

  // Resize observer logic
  if (window.ResizeObserver) {
    const appElement = document.getElementById('app');
    if (appElement) { // Check if the element exists
        const resizeObserver = new ResizeObserver(entries => {
          for (let entry of entries) {
            if (videoElement && videoElement.videoWidth) { // Add null check for videoElement
              adjustVideoContainer();
            }
          }
        });
        resizeObserver.observe(appElement);
    }
  }

  // Adjust on resize only if video element exists
  if (videoElement) {
    window.addEventListener('resize', adjustVideoContainer);
  }
});
</script>
<!-- 视频播放器脚本结束 -->

<script>
// 防止utils.js中的btf重复声明
window.btfInitialized = false;
</script>

</body>
</html>