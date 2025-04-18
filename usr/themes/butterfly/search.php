<?php
/**
 * 搜索索引生成器
 * 用于生成适用于本地搜索功能的XML文件
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// 判断是否为管理员访问
$user = Typecho_Widget::widget('Widget_User');
if (!$user->hasLogin()) {
    header('HTTP/1.1 403 Forbidden');
    exit('访问被拒绝');
}

// 设置内容类型为XML
header('Content-Type: application/xml');
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
if ($isPostgreSQL) {
    // PostgreSQL查询 - 使用双引号包裹表名和字段名
    $select = $db->select('c."cid", c."title", c."text", c."created", c."slug"')
        ->from('table.contents AS c')
        ->where('c."type" = ?', 'post')
        ->where('c."status" = ?', 'publish');
} else {
    // MySQL查询
    $select = $db->select('c.cid, c.title, c.text, c.created, c.slug')
        ->from('table.contents AS c')
        ->where('c.type = ?', 'post')
        ->where('c.status = ?', 'publish');
}

// 执行查询
$posts = $db->fetchAll($select);

// 生成XML条目
foreach ($posts as $post) {
    $permalink = Typecho_Router::url('post', array('cid' => $post['cid']), Typecho_Widget::widget('Widget_Options')->index);
    // 处理内容，移除HTML标签
    $text = strip_tags(Typecho_Widget::widget('Widget_Abstract_Contents')->markdown($post['text']));
    // 限制内容长度
    $text = mb_substr($text, 0, 10000, 'utf-8');
    
    echo '<entry>';
    echo '<title>' . htmlspecialchars($post['title']) . '</title>';
    echo '<url>' . $permalink . '</url>';
    echo '<content>' . htmlspecialchars($text) . '</content>';
    echo '</entry>';
}

echo '</search>';
exit; 
