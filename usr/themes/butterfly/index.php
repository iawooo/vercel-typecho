<?php
/**
 * 自动检查并生成搜索索引文件
 */
$searchXmlPath = __DIR__ . '/search.xml';
$searchXmlAgeThreshold = 24 * 60 * 60; // 24小时的秒数

// 检查search.xml文件是否存在或超过更新阈值
if (!file_exists($searchXmlPath) || (time() - filemtime($searchXmlPath)) > $searchXmlAgeThreshold) {
    try {
        $searchPhpPath = __DIR__ . '/search.php';
        if (file_exists($searchPhpPath)) {
            // 在后台运行生成搜索索引
            if (function_exists('exec')) {
                exec("php {$searchPhpPath} > {$searchXmlPath} 2>/dev/null &");
            } else {
                // 如果exec函数不可用，直接通过HTTP请求生成
                $searchUrl = rtrim(Helper::options()->siteUrl, '/') . '/search.php';
                $ch = curl_init($searchUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 5秒超时
                $result = curl_exec($ch);
                curl_close($ch);
                
                if ($result) {
                    file_put_contents($searchXmlPath, $result);
                }
            }
        }
    } catch (Exception $e) {
        // 仅记录错误，不中断页面加载
        error_log("自动生成搜索索引失败: " . $e->getMessage());
    }
}

/**
 * <span>主题最新版本：<span id="latest">获取中...</span><script>
fetch('https://ty.wehao.org')
  .then(res => {
    if (!res.ok) throw new Error('Network response was not ok');
    return res.json();
  })
  .then(data => {
    if (data && data.ver) {
      document.getElementById("latest").textContent = data.ver;
    } else {
      document.getElementById("latest").textContent = "无法获取";
    }
  })
  .catch(error => {
    console.error('获取版本信息失败:', error);
    document.getElementById("latest").textContent = "获取失败";
  });
</script></span>
 * 这是 Typecho 版本的 butterfly 主题
 * 主题为移植至Typecho，你可以替换原butterfly主题的index.css文件
 * 当前适配 hexo-butterfly 4.6.0
 * <a href="https://www.wehaox.com">个人网站</a> | <a href="https://blog.wehaox.com/archives/typecho-butterfly.html">主题使用文档</a>
 * @package Typecho-Butterfly
 * @author b站:wehao-
 * @version 1.7.9
 * @link https://space.bilibili.com/34174433
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/** 文章置顶 */
$sticky = $this->options->sticky_cids;
if ($sticky && $this->is('index') || $this->is('front')) {
    $sticky_cids = explode(',', strtr($sticky, ' ', ',')); //分割文本 
    $sticky_html = "<span class='article-meta'><i class='fas fa-thumbtack article-meta__icon sticky'></i><span class='sticky'>置顶 </span><span class='article-meta__separator'>|</span></span>";
    $db = Typecho_Db::get();
    $dbType = explode('_', $db->getAdapterName())[0];
    $isPostgreSQL = ($dbType == 'Pgsql' || $dbType == 'Postgresql' || $dbType == 'Pdo' && strpos($db->getAdapterName(), 'Pgsql') !== false);
    
    try {
        $select1 = $this->select()->where('type = ?', 'post');
        $select2 = $this->select()->where('type = ? AND status = ? AND created < ?', 'post', 'publish', time());
        $this->row = [];
        $this->stack = [];
        $this->length = 0;
        $order = '';
        
        // 针对不同数据库使用不同的查询方式
        if ($isPostgreSQL) {
            // PostgreSQL的case语句语法
            $case_stmt = 'CASE "cid" ';
            foreach ($sticky_cids as $i => $cid) {
                if ($i == 0) $select1->where('cid = ?', $cid);
                else $select1->orWhere('cid = ?', $cid);
                $case_stmt .= "WHEN $cid THEN $i ";
                $select2->where('cid != ?', $cid);
            }
            $case_stmt .= 'ELSE 999 END';
            
            if ($sticky_cids) $select1->order('', $case_stmt);
        } else {
            // MySQL的case语句语法
            foreach ($sticky_cids as $i => $cid) {
                if ($i == 0) $select1->where('cid = ?', $cid);
                else $select1->orWhere('cid = ?', $cid);
                $order .= " when $cid then $i";
                $select2->where('table.contents.cid != ?', $cid);
            }
            if ($order) $select1->order('', "(case cid$order else 999 end)");
        }
        
        if ($this->_currentPage == 1) {
            foreach ($db->fetchAll($select1) as $sticky_post) {
                $sticky_post['sticky'] = $sticky_html;
                $this->push($sticky_post);
            }
        }
        
        $uid = $this->user->uid; //登录时，显示用户各自的私密文章
        if ($uid) {
            if ($isPostgreSQL) {
                $select2->orWhere('author_id = ? AND status = ?', $uid, 'private');
            } else {
                $select2->orWhere('authorId = ? AND status = ?', $uid, 'private');
            }
        }
        
        if ($isPostgreSQL) {
            // 在PostgreSQL中确保列名使用双引号
            $sticky_posts = $db->fetchAll($select2->order('"created"', Typecho_Db::SORT_DESC)->page($this->_currentPage, $this->parameter->pageSize));
        } else {
            $sticky_posts = $db->fetchAll($select2->order('table.contents.created', Typecho_Db::SORT_DESC)->page($this->_currentPage, $this->parameter->pageSize));
        }
        
        foreach ($sticky_posts as $sticky_post) $this->push($sticky_post); //压入列队
        $this->setTotal($this->getTotal() - count($sticky_cids)); //置顶文章不计算在所有文章内
    } catch (Exception $e) {
        // 当查询出错时记录错误信息
        error_log("置顶文章查询错误: " . $e->getMessage());
    }
}
?>
<?php $this->need('header.php'); ?>
<main class="layout" id="content-inner">
    <div class="recent-posts" id="recent-posts">
        <?php
        if ($this->options->googleadsense != "") :
            $i = 1;
            if ($this->options->pageSize <= 5) {
                $k = $m = $g = 3;
            } else if ($this->options->pageSize == 10) {
                $k = rand(3, 4);
                $m = rand(6, 8);
                $g = rand(10, 12);
            } else if ($this->options->pageSize > 5 && $this->options->pageSize < 10) {
                $k = $m = $g = 4;
            }
        endif;
        while ($this->next()) :
            if ($this->options->googleadsense != "") :
                if ($i == $k || $i == $m || $i == $g) {
        ?>
                    <div class="recent-post-item ads-wrap">
                        <ins class="adsbygoogle" style="display:block;height:200px;width:100%;" data-ad-format="fluid" data-ad-client="<?php $this->options->googleadsense(); ?>"></ins>
                        <script>
                            (adsbygoogle = window.adsbygoogle || []).push({});
                        </script>
                    </div>
            <?php
                    $i++;
                }
                $i++;
            endif;
            ?>
            <div class="recent-post-item" data-aos="zoom-in-up" data-aos-easing="ease-out" data-aos-duration="4000" data-aos-delay="500" data-aos-offset="200" data-aos-mirror="true">
                <?php if (noCover($this)) : ?>
                    <wehao class="post_cover">
                        <a href="<?php $this->permalink() ?>">
                            <img class="post-bg" data-lazy-src="<?php echo get_ArticleThumbnail($this); ?>" src="<?php echo GetLazyLoad() ?>" onerror="this.onerror=null;this.src='<?php $this->options->themeUrl('img/404.jpg'); ?>'"></a>
                    </wehao>
                <?php endif ?>
                <div class="recent-post-info<?php echo noCover($this) ? '' : ' no-cover'; ?>">
                    <a class="article-title" href="<?php $this->permalink() ?>"><?php $this->title() ?></a>
                    <div class="article-meta-wrap">
                        <?php $this->sticky(); ?>
                        <span class="post-meta-date">
                            <i class="far fa-calendar-alt"></i>
                            <span class="article-meta-label">发表于</span>
                            <span datetime="<?php $this->date('Y-m-d'); ?>" style="display: inline;" pubdate><?php $this->date('Y-m-d'); ?></span>
                        </span>
                        <span class="post-meta-date">
                            <span class="article-meta-separator">|</span>
                            <i class="fas fa-history"></i>
                            <span class="article-meta-label">更新于</span>
                            <span datetime="<?php echo date('Y-m-d', $this->modified); ?>" style="display: inline;"><?php echo date('Y-m-d', $this->modified); ?></span>
                        </span>
                        <span class="article-meta">
                            <span class="article-meta-separator">|</span>
                            <i class="fas fa-inbox"></i>
                            <?php $this->category(' '); ?>
                        </span>
                        <span class="article-meta">
                            <span class="article-meta-separator">|</span>
                            <i class="fa-solid fa-pen-nib"></i>
                            <?php _e('作者: '); ?><a itemprop="name" href="<?php $this->author->permalink(); ?>" rel="author"><?php $this->author(); ?></a>
                        </span>
                        <!--注释掉评论
            <span class="article-meta">
                <span class="article-meta-separator">|</span>
                <i class="fas fa-comments"></i>
                <a class="twikoo-count" href="<?php $this->permalink() ?>#comments"><?php $this->commentsNum('0条评论', '1 条评论', '%d 条评论'); ?></a>
            </span>
            -->
                    </div>
                    <div class="content">
                        <?php summaryContent($this);
                        echo '<br><a href="', $this->permalink(), '" title="', $this->title(), '">阅读全文...</a>';
                        ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
        <nav id="pagination">
            <?php $this->pageNav('<i class="fas fa-chevron-left fa-fw"></i>', '<i class="fas fa-chevron-right fa-fw"></i>', 1, '...', array('wrapTag' => 'div', 'wrapClass' => 'pagination', 'itemTag' => '', 'prevClass' => 'extend prev', 'nextClass' => 'extend next', 'currentClass' => 'page-number current')); ?>
        </nav>
    </div>
    <?php $this->need('sidebar.php'); ?>
</main>
<?php $this->need('footer.php'); ?>
<script>
// 站点信息和版本显示
function ver() {
    console.log('fugu基于typecho-butterfly7.9修改');
}

// 全局错误处理
window.addEventListener('error', function(event) {
    // 检查是否为JSON解析错误
    if (event.message && event.message.includes('Unexpected token')) {
        console.error('JSON解析错误已捕获，可能是API返回了非JSON格式的数据');
        event.preventDefault(); // 防止错误继续冒泡
    }
});

// 防止空对象引用错误
window.addEventListener('DOMContentLoaded', function() {
    // 修复常见的空对象引用错误
    const commonSelectors = {
        'subtitle': '#subtitle',
        'activity': '#activity',
        'search-button': '#search-button',
        'footer-wrap': '#footer-wrap'
    };
    
    // 检查并处理可能的空对象
    for (const [name, selector] of Object.entries(commonSelectors)) {
        const element = document.querySelector(selector);
        if (!element) {
            console.log(`元素未找到: ${name} (${selector})`);
            
            // 为了防止其他脚本尝试在这个空元素上添加事件监听器，我们可以创建一个空元素
            if (name === 'search-button' || name === 'subtitle') {
                // 只为关键元素创建替代品
                const dummy = document.createElement('div');
                dummy.id = selector.substring(1); // 移除前导#
                dummy.style.display = 'none';
                dummy.setAttribute('data-dummy', 'true');
                
                // 添加一个空的addEventListener方法，防止错误
                dummy.addEventListener = function(event, handler) {
                    console.log(`添加事件监听器失败，目标元素不存在: ${selector}, 事件: ${event}`);
                    // 不做任何事情，只是防止错误
                    return dummy;
                };
                
                // 添加到body中
                document.body.appendChild(dummy);
                console.log(`为 ${selector} 创建了替代元素防止错误`);
            }
        }
    }
    
    // 保护所有可能的addEventListener调用
    const originalAddEventListener = Element.prototype.addEventListener;
    Element.prototype.addEventListener = function(type, listener, options) {
        try {
            return originalAddEventListener.call(this, type, listener, options);
        } catch (e) {
            console.error(`addEventListener调用失败: ${e.message}`);
            // 返回this以支持链式调用
            return this;
        }
    };
});
</script>
