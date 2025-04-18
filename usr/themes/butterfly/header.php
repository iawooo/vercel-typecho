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
                    background-color: transparent;
                    padding: 20px;
                    border-radius: 10px;
                    text-align: center;
                    z-index: 10;
                    width: 300px;
                }
                
                .author-info-box.dark-mode {
                    color: #fff;
                }
                
                .author-avatar {
                    width: 100px;
                    height: 100px;
                    border-radius: 50%;
                    margin: 0 auto 10px;
                    overflow: hidden;
                    border: 3px solid rgba(255, 255, 255, 0.6);
                }
                
                .author-avatar img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }
                
                .author-name {
                    font-size: 22px;
                    font-weight: bold;
                    margin-bottom: 10px;
                    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
                    color: #fff;
                }
                
                .author-description {
                    font-size: 16px;
                    line-height: 1.5;
                    margin-bottom: 10px;
                    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
                    color: #fff;
                }
                </style>

                <!-- 添加的作者信息模块 -->
                <div class="author-info-box" id="author-info-box">
                    <div class="author-avatar">
                        <img src="<?php $this->options->logoUrl() ?>" alt="author avatar">
                    </div>
                    <div class="author-name"><?php $this->author(); ?></div>
                    <div class="author-description"><?php $this->options->author_description() ?></div>
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
            </div>
            <div id="scroll-down"><i class="fas fa-angle-down scroll-down-effects"></i></div>
            <?php else : ?>
            <header class="not-top-img" id="page-header">
                <?php endif; ?>
                <?php $this->need('public/nav.php'); ?>
            </header>