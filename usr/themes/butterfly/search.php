<?php
/**
 * 搜索索引生成器
 * 用于生成适用于本地搜索功能的XML文件
 */
if (!defined('__TYPECHO_ROOT_DIR__')) {
    define('__TYPECHO_ROOT_DIR__', dirname(__FILE__));
    require_once __TYPECHO_ROOT_DIR__ . '/config.inc.php';
    
    // 确保Typecho应用被初始化
    $db = Typecho_Db::get();
    Typecho_Widget::widget('Widget_Init');
}

// 设置内容类型为XML
header('Content-Type: application/xml');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

try {
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<search>';

    // 获取数据库实例
    $db = Typecho_Db::get();
    $adapter = $db->getAdapterName();
    $isPostgreSQL = false;

    // 判断是否为PostgreSQL数据库
    if (strpos($adapter, 'Pgsql') !== false) {
        $isPostgreSQL = true;
    }

    // 根据数据库类型生成不同的SQL查询
    try {
        if ($isPostgreSQL) {
            // PostgreSQL查询 - 使用双引号包裹表名和字段名
            $prefix = $db->getPrefix();
            $select = $db->select()->from('table.contents')
                ->where('table.contents.type = ?', 'post')
                ->where('table.contents.status = ?', 'publish');
                
            // 修改PostgreSQL的查询语法
            $sql = $db->fix($select, array('table' => 'contents', 'table' => 'contents'));
            
            // 使用fetchAll直接获取结果集
            $posts = $db->fetchAll($sql);
        } else {
            // MySQL查询
            $select = $db->select('c.cid, c.title, c.text, c.created, c.slug')
                ->from('table.contents AS c')
                ->where('c.type = ?', 'post')
                ->where('c.status = ?', 'publish');
                
            // 执行查询
            $posts = $db->fetchAll($select);
        }

        if (empty($posts)) {
            echo '<entry><title>没有文章</title><content>没有找到任何已发布的文章</content><url>/</url></entry>';
        } else {
            // 记录查询过程中的错误
            $errors = array();
            
            // 生成XML条目
            foreach ($posts as $post) {
                try {
                    // 对PostgreSQL的特殊处理
                    $cid = $isPostgreSQL ? $post['cid'] : $post['cid'];
                    
                    // 获取文章永久链接
                    $options = Typecho_Widget::widget('Widget_Options');
                    $permalink = Typecho_Router::url('post', array('cid' => $cid), $options->index);
                    
                    // 处理内容，移除HTML标签
                    $text = isset($post['text']) ? $post['text'] : '';
                    $text = strip_tags(Typecho_Widget::widget('Widget_Abstract_Contents')->markdown($text));
                    
                    // 限制内容长度
                    $text = mb_substr($text, 0, 10000, 'utf-8');
                    
                    // 文章标题
                    $title = isset($post['title']) ? $post['title'] : '无标题文章';
                    
                    echo '<entry>';
                    echo '<title>' . htmlspecialchars($title) . '</title>';
                    echo '<url>' . $permalink . '</url>';
                    echo '<content>' . htmlspecialchars($text) . '</content>';
                    echo '</entry>';
                } catch (Exception $e) {
                    // 略过单个文章的错误，继续处理其他文章
                    $errors[] = "文章ID: {$post['cid']}: " . $e->getMessage();
                    error_log("搜索索引生成错误 - 文章ID: {$post['cid']}: " . $e->getMessage());
                }
            }
            
            // 如果处理过程中有错误，添加一个特殊条目
            if (!empty($errors)) {
                echo '<entry><title>处理警告</title><content>在生成索引时遇到一些问题，但大部分内容已成功生成。</content><url>/</url></entry>';
            }
        }
    } catch (Exception $e) {
        echo '<entry><title>数据库错误</title><content>查询数据库时发生错误: ' . htmlspecialchars($e->getMessage()) . '</content><url>/</url></entry>';
        error_log("搜索索引生成错误 - 数据库: " . $e->getMessage());
    }

    echo '</search>';
} catch (Exception $e) {
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<search><entry><title>系统错误</title><content>生成搜索索引时发生错误: ' . htmlspecialchars($e->getMessage()) . '</content><url>/</url></entry></search>';
    error_log("搜索索引生成错误: " . $e->getMessage());
}
exit; 
