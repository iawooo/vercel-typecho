<?php
/**
 * 搜索索引生成器页面
 * 
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// 获取当前主题URL
$themeUrl = Helper::options()->themeUrl;
$siteUrl = Helper::options()->siteUrl;

// 判断是否为管理员访问
$user = Typecho_Widget::widget('Widget_User');
if (!$user->hasLogin()) {
    // 重定向到登录页
    header('Location: ' . $siteUrl . 'admin/login.php');
    exit;
}

// 处理表单提交
$message = '';
if (isset($_POST['generate']) && $_POST['generate'] == 'true') {
    // 调用搜索索引生成器
    $searchXmlContent = file_get_contents($siteUrl . 'search.php');
    
    // 保存为search.xml文件
    $searchXmlPath = __TYPECHO_ROOT_DIR__ . '/search.xml';
    file_put_contents($searchXmlPath, $searchXmlContent);
    
    $message = '搜索索引已成功生成！文件保存在根目录的 search.xml';
}

$this->need('header_com.php');
?>

<div id="body-wrap">
    <main class="layout" id="content-inner">
        <div id="page">
            <div class="article-container">
                <h1 class="article-title">搜索索引生成器</h1>
                
                <?php if ($message): ?>
                <div class="notification is-success">
                    <?php echo $message; ?>
                </div>
                <?php endif; ?>
                
                <div class="article-content">
                    <p>这个页面用于生成本地搜索功能所需的索引文件。点击下方按钮生成搜索索引。</p>
                    <p>注意：生成过程可能需要一些时间，尤其是在文章数量较多的情况下。</p>
                    
                    <form method="post" action="">
                        <input type="hidden" name="generate" value="true">
                        <button type="submit" class="button">生成搜索索引</button>
                    </form>
                    
                    <h2>使用说明</h2>
                    <p>1. 搜索索引文件将保存为站点根目录下的 search.xml 文件</p>
                    <p>2. 每当添加新文章或修改现有文章后，应该重新生成索引</p>
                    <p>3. 生成的索引支持PostgreSQL和MySQL数据库</p>
                    
                    <h2>自动更新</h2>
                    <p>你也可以设置一个定时任务定期访问 <?php echo $siteUrl; ?>search.php 来自动更新索引</p>
                </div>
            </div>
        </div>
    </main>
</div>

<?php $this->need('footer.php'); ?> 
