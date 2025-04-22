<?php
// 设置会话（如果尚未启动）
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 数据存储路径
$dataDir = dirname(__FILE__) . '/data';
if (!file_exists($dataDir)) {
    mkdir($dataDir, 0755, true);
}

$statsFile = $dataDir . '/visitor_stats.json';
$sessionsFile = $dataDir . '/active_sessions.json';
$sessionTimeout = 300; // 5分钟会话超时

// 加载统计数据
function loadStats() {
    global $statsFile;
    if (file_exists($statsFile)) {
        $data = json_decode(file_get_contents($statsFile), true);
        if (is_array($data)) {
            return $data;
        }
    }
    return ['totalVisits' => 100, 'lastUpdate' => time()];
}

// 保存统计数据
function saveStats($stats) {
    global $statsFile;
    file_put_contents($statsFile, json_encode($stats));
}

// 加载活跃会话
function loadSessions() {
    global $sessionsFile;
    if (file_exists($sessionsFile)) {
        $data = json_decode(file_get_contents($sessionsFile), true);
        if (is_array($data)) {
            return $data;
        }
    }
    return [];
}

// 保存活跃会话
function saveSessions($sessions) {
    global $sessionsFile;
    file_put_contents($sessionsFile, json_encode($sessions));
}

// 获取唯一用户标识（结合IP和User-Agent）
function getUserIdentifier() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    // 创建一个包含会话ID、IP和User-Agent的唯一标识
    return md5(session_id() . $ip . $userAgent);
}

// 清理过期会话
function cleanupSessions() {
    global $sessionTimeout;
    $sessions = loadSessions();
    $now = time();
    $activeSessions = [];
    
    foreach ($sessions as $id => $lastActive) {
        if ($now - $lastActive < $sessionTimeout) {
            $activeSessions[$id] = $lastActive;
        }
    }
    
    saveSessions($activeSessions);
    return count($activeSessions);
}

// 主处理逻辑
$stats = loadStats();
$sessions = loadSessions();

// 处理新访问
$uniqueId = getUserIdentifier(); // 使用唯一标识而不是session_id
$now = time();
$isNewVisit = false;

if (!isset($sessions[$uniqueId])) {
    $isNewVisit = true;
    $stats['totalVisits']++;
    $stats['lastUpdate'] = $now;
    saveStats($stats);
}

// 更新会话活跃时间
$sessions[$uniqueId] = $now;
saveSessions($sessions);

// 清理过期会话并获取活跃用户数
$activeUsers = cleanupSessions();

// 输出统计数据
header('Content-Type: application/json');
echo json_encode([
    'activeUsers' => max(1, $activeUsers),  // 至少显示1个在线用户
    'totalVisits' => $stats['totalVisits'],
    'isNewVisit' => $isNewVisit
]); 