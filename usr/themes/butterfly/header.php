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
                
                /* 基础位置样式 - 其他样式已移到custom.css */
                .one_third_page {
                    height: 33.33vh !important;
                    position: relative;
                }
                </style>

                <!-- 作者信息模块 -->
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
