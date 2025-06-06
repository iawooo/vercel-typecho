<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php   
/**  
    * 标签
    *  
    * @package custom  
    * @type page
    */  
$this->need('page_header.php'); ?>
<style>
.tag-cloud-list a:first-child {
    font-size: 1.8em;
}

.tag-cloud-list a {
    font-size: 1.3em;
}

.tag-cloud-list a:nth-child(2n) {
    font-size: 2.1em;
}

@import url('https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700,800,900&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}


body {
    min-height: 100vh;
    background-color: var(--global-bg);
}

.container {
    width: 100%;
    height: 400px;
    display: flex;
    justify-content: center;
    border-bottom: 1px solid rgba(255, 255, 255, .1);
    /* 添加一个从下往上线性渐变的镜像效果，增加视觉层次感 */
    -webkit-box-reflect: below 1px linear-gradient(transparent, transparent, transparent, transparent, #0005);
    margin-bottom: 100px;
}

.cloud {
    position: relative;
    top: 50px;
    z-index: 100;

    /* 横向云朵 */
    width: 320px;
    height: 100px;
    background-color: var(--primary-color);
    border-radius: 100px;
    /* drop-shadow函数将阴影效果应用于投影图像 */
    /* filter: drop-shadow(0 0 30px var(--primary-color)); */
    filter: drop-shadow(0 0 5px var(--primary-color));
}

.cloud::before {
    content: "";
    /* 左侧小云朵 */
    width: 110px;
    height: 110px;
    background-color: var(--primary-color);
    border-radius: 50%;
    position: absolute;
    top: -50px;
    left: 40px;

    /* 右侧大云朵 */
    box-shadow: 90px 0 0 30px var(--primary-color);
}

.cloud .raintext {
    position: absolute;
    top: 80px;
    height: 20px;
    line-height: 20px;
    text-transform: uppercase;
    /* text-shadow: 0 0 3px var(--global-bg), 0 0 8px var(--global-bg); */
    transform-origin: bottom;
    animation: animate 6s linear forwards;
}

@keyframes animate {
    0% {
        transform: translateX(0);
    }

    70% {
        transform: translateY(290px);
    }

    100% {
        transform: translateY(290px);
    }
}
</style>
<main class="layout" id="content-inner">
    <div id="page">
        <div class="tag-cloud-list is-center">

            <?php $this->widget('Widget_Metas_Tag_Cloud', array('sort' => 'count', 'ignoreZeroCount' => true, 'desc' => true, 'limit' => 2000))->to($tags); ?>
            <?php while($tags->next()): ?>
            <a<?php if (!empty($this->options->beautifyBlock) && in_array('ShowColorTags',
                    $this->options->beautifyBlock)): ?>
                style="color: rgb(<?php echo(rand(0, 255)); ?>, <?php echo(rand(0,255)); ?>, <?php echo(rand(0, 255)); ?>)"
                <?php endif; ?> rel="tag" class="tagslink" href="<?php $tags->permalink(); ?>"
                title="<?php $tags->name(); ?>" style='display: inline-block; margin: 0 5px 5px 0;'>
                <?php $tags->name(); ?></a>
                <?php endwhile; ?>
        </div>
        <?php $this->need('comments.php'); ?>
    </div>
    <?php $this->need('sidebar.php'); ?>
</main>
<?php $this -> need('footer.php'); ?>