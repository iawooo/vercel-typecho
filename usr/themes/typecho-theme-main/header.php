<?php $this->need('header_com.php'); ?>
<!--加载进度条插件-->
<?php Typecho_Plugin::factory('Process')->render(); ?>

<body style="zoom: 1;">
    <div class="page" id="body-wrap">
        <?php if (is_array($this->options->beautifyBlock) && in_array('ShowTopimg', $this->options->beautifyBlock)) : ?>
        <header class="one_third_page" id="page-header" style="background-image: url(<?php $this->options->headerimg() ?>)">
            <script>
            var bg = document.documentElement.getAttribute('data-theme');
            console.log(bg);
            var imgHeader = document.getElementById('page-header');
            if (bg === 'light') {
                imgHeader.style.backgroundImage = "url(<?php $this->options->headerimg() ?>)";
            } else {
                imgHeader.style.backgroundImage = "url(<?php $this->options->headerblackimg() ?>)";
            }
            </script>
            <div id="site-info">
                <style>
                @font-face {
                    font-family: 'Lucida-Calligraphy-Italic';
                    src: url('/usr/themes/butterfly/css/Lucida-Calligraphy-Italic.ttf')format('truetype');
                }

                #site-title {
                    font-family: 'Lucida-Calligraphy-Italic';
                }
                
                /* 添加的样式 */
                .one_third_page {
                    height: 33.33vh !important;
                    position: relative;
                }
                
                .author-info-box {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background-color: rgba(255, 255, 255, 0.8);
                    padding: 20px;
                    border-radius: 10px;
                    text-align: center;
                    z-index: 10;
                    width: 300px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }
                
                .author-info-box.dark-mode {
                    background-color: rgba(0, 0, 0, 0.7);
                    color: #fff;
                }
                
                .author-avatar {
                    width: 80px;
                    height: 80px;
                    border-radius: 50%;
                    margin: 0 auto 10px;
                    overflow: hidden;
                }
                
                .author-avatar img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }
                
                .author-name {
                    font-size: 18px;
                    font-weight: bold;
                    margin-bottom: 5px;
                }
                
                .author-description {
                    font-size: 14px;
                    line-height: 1.5;
                    margin-bottom: 10px;
                }
                
                .author-data {
                    display: flex;
                    justify-content: space-around;
                    margin-top: 10px;
                }
                
                .data-item {
                    text-align: center;
                }
                
                .data-item .headline {
                    font-size: 12px;
                    color: #666;
                }
                
                .dark-mode .data-item .headline {
                    color: #ccc;
                }
                
                .data-item .number {
                    font-size: 16px;
                    font-weight: bold;
                }
                </style>

                <h1 id="site-title">May your return be graced</h1>
                <h1 id="site-title">  with the reunion of those who matter most.</h1>

                <!-- 添加的作者信息模块 -->
                <div class="author-info-box" id="author-info-box">
                    <div class="author-avatar">
                        <img src="<?php $this->options->logoUrl() ?>" alt="author avatar">
                    </div>
                    <div class="author-name"><?php $this->author(); ?></div>
                    <div class="author-description"><?php $this->options->author_description() ?></div>
                    <div class="author-data">
                        <div class="data-item">
                            <div class="headline">文章</div>
                            <div class="number">
                                <?php Typecho_Widget::widget('Widget_Stat')->to($stat); ?>
                                <?php $stat->publishedPostsNum() ?>
                            </div>
                        </div>
                        <div class="data-item">
                            <div class="headline">标签</div>
                            <div class="number">
                                <?php echo tagsNum(); ?>
                            </div>
                        </div>
                        <div class="data-item">
                            <div class="headline">分类</div>
                            <div class="number">
                                <?php Typecho_Widget::widget('Widget_Stat')->to($stat); ?>
                                <?php $stat->categoriesNum() ?>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                // 根据当前主题模式设置作者信息框的样式
                document.addEventListener('DOMContentLoaded', function() {
                    var theme = document.documentElement.getAttribute('data-theme');
                    var authorBox = document.getElementById('author-info-box');
                    
                    if (theme === 'dark') {
                        authorBox.classList.add('dark-mode');
                    } else {
                        authorBox.classList.remove('dark-mode');
                    }
                });
                </script>

                <!-- <h1 id="site-title"><?php $this->options->description() ?></h1> -->
                <!--注释掉副标题
            <div id="site-subtitle">
                <span id="subtitle"></span>
            </div>-->
            </div>
            <div id="scroll-down"><i class="fas fa-angle-down scroll-down-effects"></i></div>
            <?php else : ?>
            <header class="not-top-img" id="page-header">
                <?php endif; ?>
                <?php $this->need('public/nav.php'); ?>
            </header>
