<?php
/**
 * 搜索索引生成脚本
 * 
 * 此脚本用于为Typecho博客生成search.xml搜索索引文件
 * 调用方式: 直接访问此文件的URL
 */

// 设置执行超时
set_time_limit(300);

// 引入Typecho核心文件
define('__TYPECHO_ROOT_DIR__', dirname(__FILE__));
require_once __TYPECHO_ROOT_DIR__ . '/config.inc.php';
require_once __TYPECHO_ROOT_DIR__ . '/var/Typecho/Common.php';
require_once __TYPECHO_ROOT_DIR__ . '/var/Typecho/Db.php';
require_once __TYPECHO_ROOT_DIR__ . '/var/Typecho/Widget.php';
require_once __TYPECHO_ROOT_DIR__ . '/var/Widget/Base.php';
require_once __TYPECHO_ROOT_DIR__ . '/var/Widget/Options.php';

// 添加HTML头部
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>生成搜索索引 - Typecho</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f7fa;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .container {
            background: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 1.8rem;
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
        }
        .status {
            margin: 20px 0;
            padding: 15px;
            border-radius: 4px;
        }
        .status.success {
            background-color: #e7f7ee;
            color: #0f5132;
            border-left: 4px solid #0f5132;
        }
        .status.error {
            background-color: #f8d7da;
            color: #842029;
            border-left: 4px solid #842029;
        }
        .status.info {
            background-color: #e2f0fd;
            color: #084298;
            border-left: 4px solid #084298;
        }
        .logs {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            font-family: monospace;
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 20px;
            white-space: pre-wrap;
        }
        .logs .log-item {
            margin-bottom: 8px;
            border-bottom: 1px dashed #ddd;
            padding-bottom: 8px;
        }
        .logs .log-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #2980b9;
        }
        .btn-container {
            text-align: center;
            margin-top: 25px;
        }
        .progress-container {
            margin: 20px 0;
            background-color: #e9ecef;
            border-radius: 4px;
            height: 25px;
            overflow: hidden;
        }
        .progress-bar {
            height: 100%;
            background-color: #3498db;
            width: 0%;
            transition: width 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: bold;
        }
        @media (max-width: 600px) {
            .container {
                padding: 20px 15px;
            }
            h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>生成搜索索引</h1>
<?php

// 记录日志数组
$logs = [];
$total_posts = 0;
$processed_posts = 0;

// 创建日志记录函数
function log_message($message) {
    global $logs;
    $logs[] = date('H:i:s') . ' - ' . $message;
    if (ob_get_level() > 0) {
        ob_flush();
        flush();
    }
}

try {
    // 初始化数据库连接
    log_message("正在连接数据库...");
    $db = Typecho_Db::get();
    log_message("数据库连接成功");
    
    // 获取站点URL
    log_message("获取站点信息...");
    $options = Helper::options();
    $siteUrl = $options->siteUrl;
    log_message("站点URL: " . $siteUrl);
    
    // 设置XML文件路径
    $xmlFile = __TYPECHO_ROOT_DIR__ . '/search.xml';
    log_message("搜索XML文件将保存为: " . $xmlFile);
    
    // 创建XML文档
    log_message("开始创建XML文档...");
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->formatOutput = true;
    
    // 创建根元素
    $root = $dom->createElement('search');
    $dom->appendChild($root);
    
    // 查询所有已发布的文章
    log_message("获取所有已发布的文章...");
    $posts = $db->fetchAll($db->select('cid', 'title', 'text', 'created')
        ->from('table.contents')
        ->where('type = ?', 'post')
        ->where('status = ?', 'publish')
        ->order('created', Typecho_Db::SORT_DESC));
    
    $total_posts = count($posts);
    log_message("找到 {$total_posts} 篇文章");
    
    // 检查是否存在文章
    if (empty($posts)) {
        log_message("没有找到任何文章，将创建一个示例条目");
        
        // 创建示例条目
        $entry = $dom->createElement('entry');
        
        $title = $dom->createElement('title', '示例文章');
        $entry->appendChild($title);
        
        $link = $dom->createElement('url', $siteUrl);
        $entry->appendChild($link);
        
        $content = $dom->createElement('content', '这是一个示例文章内容，实际内容将从您的博客文章中提取。');
        $entry->appendChild($content);
        
        $root->appendChild($entry);
    } else {
        // 创建进度条信息
        echo '<div class="progress-container">';
        echo '<div class="progress-bar" id="progress-bar">0%</div>';
        echo '</div>';
        
        // 添加每篇文章到XML中
        log_message("开始处理文章内容...");
        
        foreach ($posts as $post) {
            $processed_posts++;
            $progress = round(($processed_posts / $total_posts) * 100);
            
            // 更新进度条
            echo "<script>document.getElementById('progress-bar').style.width = '{$progress}%'; document.getElementById('progress-bar').innerText = '{$progress}%';</script>";
            if (ob_get_level() > 0) {
                ob_flush();
                flush();
            }
            
            $entry = $dom->createElement('entry');
            
            // 添加标题
            $title = $dom->createElement('title');
            $title->appendChild($dom->createCDATASection(htmlspecialchars($post['title'])));
            $entry->appendChild($title);
            
            // 添加链接
            $permalink = $siteUrl . "archives/" . $post['cid'] . '/';
            $link = $dom->createElement('url');
            $link->appendChild($dom->createCDATASection($permalink));
            $entry->appendChild($link);
            
            // 处理内容
            $content = $post['text'];
            // 去除HTML标签
            $content = strip_tags($content);
            // 去除多余空白
            $content = preg_replace('/\s+/', ' ', $content);
            // 限制长度
            $content = mb_substr($content, 0, 500, 'UTF-8');
            
            // 添加内容
            $contentElem = $dom->createElement('content');
            $contentElem->appendChild($dom->createCDATASection(htmlspecialchars($content)));
            $entry->appendChild($contentElem);
            
            // 添加条目到根元素
            $root->appendChild($entry);
            
            // 每10篇文章记录一次日志
            if ($processed_posts % 10 == 0 || $processed_posts == $total_posts) {
                log_message("已处理 {$processed_posts}/{$total_posts} 篇文章");
            }
        }
    }
    
    // 保存XML文件
    log_message("准备保存XML文件...");
    
    // 检查目录是否可写
    if (!is_writable(dirname($xmlFile))) {
        throw new Exception("目录不可写: " . dirname($xmlFile));
    }
    
    // 尝试保存文件
    if ($dom->save($xmlFile)) {
        $fileSize = filesize($xmlFile);
        $fileSizeKB = round($fileSize / 1024, 2);
        log_message("XML文件保存成功! 文件大小: {$fileSizeKB} KB");
        
        // 检查文件是否可访问
        $xmlUrl = $siteUrl . 'search.xml';
        log_message("搜索XML文件网址: <a href='{$xmlUrl}' target='_blank'>{$xmlUrl}</a>");
        
        // 显示成功消息
        echo '<div class="status success">';
        echo '<h3>✅ 搜索索引生成成功!</h3>';
        echo '<p>search.xml文件已成功创建，您现在可以使用站点的搜索功能。</p>';
        echo '</div>';
    } else {
        throw new Exception("无法保存XML文件");
    }
} catch (Exception $e) {
    log_message("错误: " . $e->getMessage());
    
    // 显示错误消息
    echo '<div class="status error">';
    echo '<h3>❌ 生成失败</h3>';
    echo '<p>' . $e->getMessage() . '</p>';
    echo '<p>可能原因:</p>';
    echo '<ul>';
    echo '<li>目录权限不足 - 请确保网站根目录可写</li>';
    echo '<li>数据库连接问题 - 请确保数据库配置正确</li>';
    echo '<li>PHP版本或配置问题 - 请检查是否满足所需的PHP扩展</li>';
    echo '</ul>';
    echo '</div>';
    
    // 显示手动创建指南
    echo '<div class="status info">';
    echo '<h3>📝 手动解决方案</h3>';
    echo '<p>如果自动生成失败，您可以尝试手动创建文件:</p>';
    echo '<ol>';
    echo '<li>在您的网站根目录创建名为"search.xml"的文件</li>';
    echo '<li>使用XML编辑器或文本编辑器打开该文件</li>';
    echo '<li>将以下内容复制进去（包含您的博客文章内容）</li>';
    echo '</ol>';
    echo '</div>';
}

// 显示日志
echo '<h3>处理日志:</h3>';
echo '<div class="logs">';
foreach ($logs as $log) {
    echo '<div class="log-item">' . $log . '</div>';
}
echo '</div>';

// 添加返回按钮
echo '<div class="btn-container">';
echo '<a href="' . $siteUrl . '" class="btn">返回网站首页</a>';
echo '</div>';

?>
    </div>
    
    <script>
    // 滚动日志到底部
    window.onload = function() {
        const logs = document.querySelector('.logs');
        logs.scrollTop = logs.scrollHeight;
    };
    </script>
</body>
</html> 