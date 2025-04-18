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
                let animation = lottie.loadAnimation({
                    container: document.getElementById('activity'), // 动画容器
                    renderer: 'svg',
                    loop: true,
                    autoplay: true,
                    path: '/usr/themes/butterfly/img/activity.json' // 动画JSON文件的路径
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
    <script id="click-heart" src="<?php cdnBaseUrl() ?>/js/click-heart.min.js" async="async" mobile="false"></script>
<?php elseif ($this->options->CursorEffects !== 'off' && $this->options->CursorEffects == 'fireworks') : ?>
    <canvas class="fireworks"></canvas>
    <script id="fireworks" src="<?php cdnBaseUrl() ?>/js/fireworks.min.js" async="async" mobile="false"></script>
<?php endif; ?>
<?php if ($this->options->ShowLive2D !== 'off' && !isMobile()) : ?>
    <script src="<?php cdnBaseUrl() ?>/js/autoload.js"></script>
<?php endif; ?>
<!-- 作者防伪措施 -->
<!-- <script type="text/javascript" src="<?php $this->options->themeUrl('js/custom.main.js'); ?>"></script> -->
<script>
    <?php $this->options->CustomScript() ?>
</script>
<?php $this->options->CustomBodyEnd() ?>


<?php require_once('public/rightside.php'); ?>
<!--pjax-->
<?php if ($this->options->EnablePjax === 'on') : ?>
    <link rel="stylesheet" href="<?php cdnBaseUrl() ?>/css/nprogress.css">
    <script src="<?php cdnBaseUrl() ?>/js/pjax.min.js"></script>
    <script src="<?php cdnBaseUrl() ?>/js/nprogress.js"></script>
    <script>
        let pjaxSelectors = ["title", "#body-wrap", "#rightside-config-hide", "#rightside-config-show", ".js-pjax"];
        var pjax = new Pjax({
            elements: 'a:not([target="_blank"])',
            selectors: pjaxSelectors,
            cacheBust: !1,
            analytics: !1,
            scrollRestoration: !1
        });
        document.addEventListener("pjax:send", (function() {
                if (window.removeEventListener("scroll", window.tocScrollFn), window.removeEventListener("scroll",
                        scrollCollect), "object" == typeof preloader && preloader.initLoading(), window.aplayers)
                    for (let e = 0; e < window.aplayers.length; e++) window.aplayers[e].options.fixed || window
                        .aplayers[e].destroy();
                "object" == typeof typed && typed.destroy();
                const e = document.body.classList;
                e.contains("read-mode") && e.remove("read-mode")
                NProgress.start();
            })),
            document.addEventListener("pjax:complete", (function() {
                <?php if ($this->options->hcaptchaSecretKey !== "" && $this->options->hcaptchaAPIKey !== "") : ?>
                    hcaptcha.render('h-captcha', {
                        sitekey: '<?php $this->options->hcaptchaSecretKey() ?>'
                    });
                <?php endif ?>
                <?php $this->options->PjaxCallBack() ?>
                NProgress.done();
                window.refreshFn(),
                    document.querySelectorAll("script[data-pjax]").forEach(e => {
                        const t = document.createElement("script"),
                            o = e.text || e.textContent || e.innerHTML || "";
                        Array.from(e.attributes).forEach(e => t.setAttribute(e.name, e.value)), t.appendChild(
                            document.createTextNode(o)), e.parentNode.replaceChild(t, e)
                    }),
                    GLOBAL_CONFIG.islazyload && window.lazyLoadInstance.update(), "function" == typeof chatBtnFn &&
                    chatBtnFn(), "function" == typeof panguInit && panguInit(), "function" == typeof gtag && gtag(
                        "config", "", {
                            page_path: window.location.pathname
                        }),
                    "object" == typeof _hmt && _hmt.push(["_trackPageview", window.location.pathname]),
                    "function" == typeof loadMeting && document.getElementsByClassName("aplayer").length &&
                    loadMeting(),
                    "object" == typeof Prism && Prism.highlightAll(), "object" == typeof preloader && preloader
                    .endLoading()
                coverShow()
            })),
            document.addEventListener("pjax:error", e => {
                // 404 === e.request.status && pjax.loadUrl("/404");
                if (e.request.status === 404) {
                    window.location = "/404";
                }
                if (e.request.status === 403) {
                    window.location = e.request.responseURL
                }
            })
    </script>
<?php endif ?>
<!--搜索  -->
<style>
/* 搜索对话框样式 */
#local-search {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
}
#search-mask {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 999;
    background: rgba(0, 0, 0, 0.6);
}
.search-dialog {
    position: fixed;
    top: 10vh;
    left: 50%;
    transform: translateX(-50%);
    width: 80%;
    max-width: 900px;
    z-index: 9999;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
}
.search-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
}
.search-dialog-title {
    font-size: 18px;
    font-weight: bold;
}
.search-close-button {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
}
.search-wrap {
    padding: 20px;
}
#local-search-input input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
}
#local-search-results {
    max-height: 60vh;
    overflow-y: auto;
    margin-top: 20px;
}
/* 深色模式 */
[data-theme="dark"] .search-dialog {
    background: #222;
    color: #eee;
}
[data-theme="dark"] .search-nav {
    border-bottom: 1px solid #333;
}
[data-theme="dark"] #local-search-input input {
    background: #333;
    color: #eee;
    border-color: #444;
}
</style>
<div id="local-search" style="display: none;">
    <div class="search-dialog" style="display: none;">
        <nav class="search-nav">
            <span class="search-dialog-title">本地搜索</span>
            <span id="loading-status"></span>
            <button class="search-close-button">
                <i class="fas fa-times"></i>
            </button>
        </nav>
        <div class="search-wrap">
            <div id="local-search-input">
                <form class="local-search-box" method="post" action="<?php $this->options->siteUrl(); ?>" role="search" id="search">
                    <label for="s" class="sr-only">
                        <?php _e('搜索关键字'); ?>
                    </label>
                    <input type="text" name="s" id="s" placeholder="回车查询" required="required">
                </form>
            </div>
            <div id="loading-database" style="display: none;"><i class="fas fa-spinner fa-pulse"></i></div>
            <hr>
            <div id="local-search-results"></div>
        </div>
    </div>
    <div id="search-mask" style="display: none;"></div>
</div>
<!--搜索end  -->
<div class="js-pjax">
    <?php if (is_array($this->options->beautifyBlock) && in_array('showNoAlertSearch', $this->options->beautifyBlock)) : ?>
        <script>
            (function() {
                try {
                    const searchButton = document.getElementById('search-button');
                    const input = document.getElementById('dSearchIn');
                    
                    // 检查元素是否存在
                    if (searchButton && input) {
                        searchButton.addEventListener('click', function() {
                            try { // 添加内层 try-catch
                                input.style.width = '150px';
                                input.focus();
                            } catch (e) {
                                console.warn('Error handling search button click:', e);
                            }
                        });
                        input.addEventListener('blur', function() {
                            try { // 添加内层 try-catch
                                input.style.width = '35px';
                            } catch (e) {
                                console.warn('Error handling input blur:', e);
                            }
                        });
                    } else {
                        if (!searchButton) console.warn('Element #search-button not found for showNoAlertSearch');
                        if (!input) console.warn('Element #dSearchIn not found for showNoAlertSearch');
                    }
                } catch (error) {
                    console.error('Error in showNoAlertSearch IIFE:', error);
                }
            })()
        </script>
    <?php endif ?>
    <?php if ($this->options->hcaptchaSecretKey !== "" && $this->options->hcaptchaAPIKey !== "") : ?>
        <script src="https://hcaptcha.com/1/api.js" async defer></script>
    <?php endif ?>
    <?php if ($this->is('post') || $this->is('page')) : ?>
        <script src="<?php $this->options->themeUrl('js/comjs.js?v1.4.3'); ?>"></script>
    <?php endif ?>
    <?php if (!empty($this->options->beautifyBlock) && in_array('showButterflyClock', $this->options->beautifyBlock)) : ?>
        <script data-pjax>
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
        <script src="https://widget.qweather.net/simple/static/js/he-simple-common.js?v=2.0"></script>
        <script data-pjax src="https://cdn.cbd.int/hexo-butterfly-clock-anzhiyu/lib/clock.min.js"></script>
        <link rel="stylesheet" href="https://cdn.cbd.int/hexo-butterfly-clock-anzhiyu/lib/clock.min.css">
    <?php endif ?>
    <?php $this->header('commentReply=1&description=0&keywords=0&generator=0&template=0&pingback=0&xmlrpc=0&wlw=0&rss2=0&rss1=0&antiSpam=0&atom'); ?>
    <?php if ($this->options->NewTabLink == 'on') : ?>
        <script>
            $(document).ready(function() {
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
        <script type="text/javascript" src="<?php $this->options->themeUrl('js/wehao.js?v1.7.6'); ?>"></script>
        <style>
            #page-header:not(.not-top-img):before {
                background-color: rgba(0, 0, 0, 0);
            }
        </style>
        <!--打字-->
        <?php if (is_array($this->options->beautifyBlock) && in_array('ShowTopimg', $this->options->beautifyBlock)) : ?>
            <?php if (!empty($this->options->CustomSubtitle)) : ?>
                <script>
                    function subtitleType() {
                        if (true) {
                            // 检查subtitle元素是否存在
                            const subtitleElement = document.getElementById('subtitle');
                            if (!subtitleElement) {
                                console.log('打字机效果的目标元素不存在');
                                return;
                            }
                            
                            try {
                                var typed = new Typed("#subtitle", {
                                    strings: "<?php $this->options->CustomSubtitle() ?>".split(","),
                                    startDelay: 300,
                                    typeSpeed: 150,
                                    loop: <?php $this->options->SubtitleLoop() ?>,
                                    backSpeed: 50
                                })
                            } catch (e) {
                                console.error('Typed.js初始化出错:', e);
                            }
                        }
                    }
                    "function" == typeof Typed ? subtitleType() : getScript("/usr/themes/butterfly/js/typed.min.js")
                        .then(subtitleType)
                </script>
            <?php else : ?>
                <script>
                    function subtitleType() {
                        // 检查subtitle元素是否存在
                        const subtitleElement = document.getElementById('subtitle');
                        if (!subtitleElement) {
                            console.log('打字机效果的目标元素不存在');
                            return;
                        }
                        
                        try {
                            fetch("https://v1.hitokoto.cn")
                                .then(response => {
                                    if (!response.ok) {
                                        // 如果 HTTP 状态码不是 2xx，抛出错误
                                        throw new Error(`网络请求失败，状态码: ${response.status}`);
                                    }
                                    // 检查 Content-Type 是否为 JSON
                                    const contentType = response.headers.get("content-type");
                                    if (!contentType || !contentType.includes("application/json")) {
                                        // 如果不是 JSON，抛出类型错误
                                        throw new TypeError("收到的响应不是 JSON 格式");
                                    }
                                    // 如果一切正常，解析 JSON
                                    return response.json();
                                })
                                .then(t => {
                                    let o = " ".split(","); // 保持原有逻辑，即使看起来有点奇怪
                                    if (t && t.hitokoto) {
                                      o.unshift(t.hitokoto);
                                    } else {
                                      o.unshift("无法解析一言数据"); // 添加默认值
                                    }
                                    
                                    new Typed("#subtitle", {
                                        strings: o,
                                        startDelay: 300,
                                        typeSpeed: 150,
                                        loop: <?php $this->options->SubtitleLoop() ?>, // 假设 SubtitleLoop 是 PHP 变量
                                        backSpeed: 50
                                    });
                                })
                                .catch(error => {
                                    console.error('获取或处理一言数据出错:', error);
                                    // 发生任何错误时，使用默认值
                                    try {
                                      new Typed("#subtitle", {
                                          strings: ["加载一言失败"], // 提供更明确的错误提示
                                          startDelay: 300,
                                          typeSpeed: 150,
                                          loop: false,
                                          backSpeed: 50
                                      });
                                    } catch (typedError) {
                                        console.error('Typed.js 在错误处理中初始化失败:', typedError);
                                        if(subtitleElement) subtitleElement.textContent = '加载一言失败'; // 最终降级
                                    }
                                });
                        } catch (e) {
                            console.error('执行 subtitleType 函数时出错:', e);
                        }
                    }
                    // 确保 Typed.js 加载后再执行，或者直接执行（如果已加载）
                    if (typeof Typed === "function") {
                         subtitleType();
                    } else {
                         // 注意：这里的 getScript 实现需要存在于全局作用域或可以被访问
                         if (typeof getScript === 'function') {
                            getScript("/usr/themes/butterfly/js/typed.min.js").then(subtitleType).catch(err => console.error('加载 typed.min.js 失败:', err));
                         } else {
                            console.error('getScript 函数未定义，无法加载 Typed.js');
                         }
                    }
                </script>
            <?php endif ?>
        <?php endif ?>
        <!--打字end-->
        <!--判断主页end-->
    <?php endif ?>
    <?php if (!empty($this->options->slide_cids) && $this->is('index')) : ?>
        <script data-pjax>
            function butterfly_swiper_injector_config() {
                var parent_div_git = document.getElementById('recent-posts');
                var item_html =
                    `<div class="recent-post-item" style="height: auto;width: 100%"><div class="blog-slider swiper-container-fade swiper-container-horizontal" id="swiper_container"><div class="blog-slider__wrp swiper-wrapper" style="transition-duration: 0ms;">
                                                                          <?php
                                                                            $slide_cids = $this->options->slide_cids;
                                                                            $cid = explode(',', strtr($slide_cids, ' ', ','));
                                                                            $num = count($cid);
                                                                            $html = "";
                                                                            for ($i = 0; $i < $num; $i++) {
                                                                                $this->widget('Widget_Archive@post' . $i, 'pageSize=1&type=post', 'cid=' . $cid[$i])->to($ji);
                                                                                $html = $html . '<div class="blog-slider__item swiper-slide" style="width: 750px; opacity: 1; transform: translate3d(0px, 0px, 0px); transition-duration: 0ms;"><a class="blog-slider__img" href="' . $ji->permalink . '" alt=""><img width="48" height="48" src="' . get_ArticleThumbnail($ji) . '" alt="" /></a><div class="blog-slider__content"><span class="blog-slider__code">' . date('Y-m-d', $ji->modified) . '</span><a class="blog-slider__title" href="' . $ji->permalink . '" alt="' . $ji->title . '">' . $ji->title . '</a><div class="blog-slider__text">' . $ji->title . '</div><a class="blog-slider__button" href="' . $ji->permalink . '" alt="">详情</a></div></div>';
                                                                            }
                                                                            echo $html;
                                                                            ?>
                                                                          </div><div class="blog-slider__pagination swiper-pagination-clickable swiper-pagination-bullets"></div></div></div>`;
                parent_div_git.innerHTML = item_html + parent_div_git.innerHTML // 无报错，但不影响使用(支持pjax跳转)
                // parent_div_git.insertAdjacentHTML("afterbegin", item_html) // 有报错，但不影响使用(支持pjax跳转)
            }
            if (document.getElementById('recent-posts') && (location.pathname === 'all' || 'all' === 'all')) {
                butterfly_swiper_injector_config()
            }
        </script>
        <script src="https://npm.elemecdn.com/hexo-butterfly-swiper/lib/swiper.min.js"></script>
        <script data-pjax src="https://npm.elemecdn.com/hexo-butterfly-swiper/lib/swiper_init.js"></script>
        <link rel="stylesheet" href="https://npm.elemecdn.com/hexo-butterfly-swiper/lib/swiperstyle.css">
        <link rel="stylesheet" href="https://npm.elemecdn.com/hexo-butterfly-swiper/lib/swiper.min.css">
    <?php endif ?>
</div>
<!--js-pjax end-->
<?php require_once('public/rightside.php'); ?>
<!--pjax-->
<?php if ($this->options->EnablePjax === 'on') : ?>
    <link rel="stylesheet" href="<?php cdnBaseUrl() ?>/css/nprogress.css">
    <script src="<?php cdnBaseUrl() ?>/js/pjax.min.js"></script>
    <script src="<?php cdnBaseUrl() ?>/js/nprogress.js"></script>
    <script>
        let pjaxSelectors = ["title", "#body-wrap", "#rightside-config-hide", "#rightside-config-show", ".js-pjax"];
        var pjax = new Pjax({
            elements: 'a:not([target="_blank"])',
            selectors: pjaxSelectors,
            cacheBust: !1,
            analytics: !1,
            scrollRestoration: !1
        });
        document.addEventListener("pjax:send", (function() {
                if (window.removeEventListener("scroll", window.tocScrollFn), window.removeEventListener("scroll",
                        scrollCollect), "object" == typeof preloader && preloader.initLoading(), window.aplayers)
                    for (let e = 0; e < window.aplayers.length; e++) window.aplayers[e].options.fixed || window
                        .aplayers[e].destroy();
                "object" == typeof typed && typed.destroy();
                const e = document.body.classList;
                e.contains("read-mode") && e.remove("read-mode")
                NProgress.start();
            })),
            document.addEventListener("pjax:complete", (function() {
                <?php if ($this->options->hcaptchaSecretKey !== "" && $this->options->hcaptchaAPIKey !== "") : ?>
                    hcaptcha.render('h-captcha', {
                        sitekey: '<?php $this->options->hcaptchaSecretKey() ?>'
                    });
                <?php endif ?>
                <?php $this->options->PjaxCallBack() ?>
                NProgress.done();
                window.refreshFn(),
                    document.querySelectorAll("script[data-pjax]").forEach(e => {
                        const t = document.createElement("script"),
                            o = e.text || e.textContent || e.innerHTML || "";
                        Array.from(e.attributes).forEach(e => t.setAttribute(e.name, e.value)), t.appendChild(
                            document.createTextNode(o)), e.parentNode.replaceChild(t, e)
                    }),
                    GLOBAL_CONFIG.islazyload && window.lazyLoadInstance.update(), "function" == typeof chatBtnFn &&
                    chatBtnFn(), "function" == typeof panguInit && panguInit(), "function" == typeof gtag && gtag(
                        "config", "", {
                            page_path: window.location.pathname
                        }),
                    "object" == typeof _hmt && _hmt.push(["_trackPageview", window.location.pathname]),
                    "function" == typeof loadMeting && document.getElementsByClassName("aplayer").length &&
                    loadMeting(),
                    "object" == typeof Prism && Prism.highlightAll(), "object" == typeof preloader && preloader
                    .endLoading()
                coverShow()
            })),
            document.addEventListener("pjax:error", e => {
                // 404 === e.request.status && pjax.loadUrl("/404");
                if (e.request.status === 404) {
                    window.location = "/404";
                }
                if (e.request.status === 403) {
                    window.location = e.request.responseURL
                }
            })
    </script>
<?php endif ?>

<!--pjax end-->
<!-- AI摘要机器人 -->
<!-- <script>
  let tianliGPT_postSelector = '#post #article-container';
  let tianliGPT_wordLimit = 1000;
  // let tianliGPT_postURL = "*/archives/*.html";
  let tianliGPT_key = '3841b0be5627b3d67830';
</script>
<script src="https://cdn1.tianli0.top/gh/zhheo/Post-Abstract-AI@0.15.2/tianli_gpt.min.js"></script> -->
<!-- AOS动画库 -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        AOS.init();
        console.log('aos init!');
    });
</script>

<!-- 引入本地搜索脚本 -->
<script src="<?php $this->options->themeUrl('/js/local-search.js?v=1.1.0'); ?>"></script>

<!-- 内联搜索脚本 (已被注释掉) -->
<!-- 
<script>
// ... (注释掉的内联搜索代码)
</script>
-->

</body>
</html>