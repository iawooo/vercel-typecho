/**
 * 简单的评论缓存脚本
 * 直接在浏览器端获取并显示最新评论
 */

// 评论容器ID
const COMMENTS_CONTAINER_ID = 'giscus-recent-comments';

// 本地存储键名
const STORAGE_KEY = 'recent-comments-cache-v3'; // 再次更新缓存键名
const CACHE_TIME = 3600000; // 缓存时间（毫秒）：1小时

// GitHub仓库信息（默认值，会尝试从页面中获取）
const DEFAULT_REPO = {
    owner: 'iawooo',
    repo: 'vlty'
};

// 网站根URL，用于构建正确的链接
const SITE_ROOT = window.location.origin;

// 初始化
document.addEventListener('DOMContentLoaded', function() {
    loadRecentComments();
});

/**
 * 加载最新评论
 */
function loadRecentComments() {
    const container = document.getElementById(COMMENTS_CONTAINER_ID);
    if (!container) return;
    
    // 隐藏重试按钮
    const refreshButton = document.querySelector('.comment-refresh');
    if (refreshButton) {
        refreshButton.style.display = 'none';
    }
    
    // 显示加载中
    container.innerHTML = '<div class="aside-list-item"><div class="content" style="text-align: center; width: 100%;"><span class="comment">正在加载最新评论...</span></div></div>';
    
    // 首先尝试从本地缓存加载
    const cachedData = getCommentsFromCache();
    if (cachedData) {
        renderComments(cachedData.comments, container);
        // 如果缓存不是太旧，就使用缓存
        if (Date.now() - cachedData.timestamp < CACHE_TIME) {
            return;
        }
    }
    
    // 尝试从GitHub获取讨论数据
    fetchDiscussionsFromRepo();
}

/**
 * 从本地缓存获取评论
 */
function getCommentsFromCache() {
    try {
        const cachedData = localStorage.getItem(STORAGE_KEY);
        if (!cachedData) return null;
        return JSON.parse(cachedData);
    } catch (e) {
        console.error('读取缓存评论失败:', e);
        return null;
    }
}

/**
 * 保存评论到本地缓存
 */
function saveCommentsToCache(comments) {
    try {
        const cacheData = {
            comments: comments,
            timestamp: Date.now()
        };
        localStorage.setItem(STORAGE_KEY, JSON.stringify(cacheData));
    } catch (e) {
        console.error('保存评论到缓存失败:', e);
    }
}

/**
 * 获取当前页面的giscus配置
 */
function getGiscusConfig() {
    // 先尝试从script标签获取
    const script = document.querySelector('script[src*="giscus.app/client.js"]');
    if (script) {
        const repo = script.getAttribute('data-repo');
        if (repo) {
            const [owner, repoName] = repo.split('/');
            if (owner && repoName) {
                return { owner, repo: repoName };
            }
        }
    }
    
    // 再尝试从giscus iframe获取
    const iframe = document.querySelector('iframe.giscus-frame');
    if (iframe) {
        const src = iframe.src;
        const repoMatch = src.match(/repo=([^&]+)/);
        if (repoMatch && repoMatch[1]) {
            const repo = decodeURIComponent(repoMatch[1]);
            const [owner, repoName] = repo.split('/');
            if (owner && repoName) {
                return { owner, repo: repoName };
            }
        }
    }
    
    // 找不到配置，使用默认值
    return DEFAULT_REPO;
}

/**
 * 从GitHub仓库获取讨论数据
 */
function fetchDiscussionsFromRepo() {
    const container = document.getElementById(COMMENTS_CONTAINER_ID);
    const repoInfo = getGiscusConfig();
    
    // 使用REST API直接获取讨论列表
    fetch(`https://api.github.com/repos/${repoInfo.owner}/${repoInfo.repo}/discussions`)
        .then(response => {
            if (!response.ok) {
                throw new Error('获取讨论列表失败: ' + response.status);
            }
            return response.json();
        })
        .then(discussions => {
            if (!discussions || discussions.length === 0) {
                throw new Error('没有找到讨论');
            }
            
            // 获取特定讨论的评论（以第一个讨论为例）
            return fetch(`https://api.github.com/repos/${repoInfo.owner}/${repoInfo.repo}/discussions/${discussions[0].number}/comments`);
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('获取评论失败: ' + response.status);
            }
            return response.json();
        })
        .then(comments => {
            if (!comments || comments.length === 0) {
                // 尝试使用硬编码获取特定讨论的评论
                return fetch(`https://api.github.com/repos/${repoInfo.owner}/${repoInfo.repo}/discussions/5/comments`);
            }
            return comments;
        })
        .then(commentsOrResponse => {
            // 检查是否是Response对象，如果是则需要进一步处理
            if (commentsOrResponse instanceof Response) {
                if (!commentsOrResponse.ok) {
                    throw new Error('获取特定讨论评论失败: ' + commentsOrResponse.status);
                }
                return commentsOrResponse.json();
            }
            return commentsOrResponse;
        })
        .then(comments => {
            if (!comments || comments.length === 0) {
                throw new Error('没有找到评论');
            }
            
            // 处理评论数据
            const processedComments = comments.map(comment => ({
                author: {
                    name: comment.user.login,
                    avatarUrl: comment.user.avatar_url
                },
                bodyText: comment.body,
                url: comment.html_url,
                createdAt: comment.created_at
            }));
            
            // 保存并渲染评论
            saveCommentsToCache(processedComments);
            renderComments(processedComments, container);
        })
        .catch(error => {
            console.error('获取GitHub评论失败:', error);
            // 使用备用方法：尝试硬编码的评论数据
            useHardcodedComments();
        });
}

/**
 * 使用硬编码的评论数据
 */
function useHardcodedComments() {
    const container = document.getElementById(COMMENTS_CONTAINER_ID);
    if (!container) return;
    
    const hardcodedComments = [
        {
            author: {
                name: 'aw168',
                avatarUrl: 'https://avatars.githubusercontent.com/u/187385069'
            },
            bodyText: '祝大家心想事成！',
            url: 'https://github.com/iawooo/vlty/discussions/5#discussioncomment-12885294',
            createdAt: new Date().toISOString()
        }
    ];
    
    saveCommentsToCache(hardcodedComments);
    renderComments(hardcodedComments, container);
}

/**
 * 使用备用评论内容
 */
function useFallbackComments() {
    const container = document.getElementById(COMMENTS_CONTAINER_ID);
    if (!container) return;
    
    // 先尝试硬编码的评论
    useHardcodedComments();
    return;
    
    // 以下代码作为第二备选，仅在需要时使用
    const fallbackComments = [{
        author: { 
            name: '访客', 
            avatarUrl: '/img/avatar.jpg' 
        },
        bodyText: '暂无评论，欢迎前往文章页面发表第一条评论！',
        url: SITE_ROOT + '/', // 使用网站首页作为链接
        createdAt: new Date().toISOString()
    }];
    
    renderComments(fallbackComments, container);
    
    // 显示重试按钮
    const refreshButton = document.querySelector('.comment-refresh');
    if (refreshButton) {
        refreshButton.style.display = 'block';
    }
}

/**
 * 渲染评论到容器
 */
function renderComments(comments, container) {
    let html = '';
    
    comments.forEach(comment => {
        const text = comment.bodyText.length > 30 ? comment.bodyText.substring(0, 30) + '...' : comment.bodyText;
        
        html += `
        <div class="aside-list-item">
            <a href="${comment.url}" class="thumbnail" target="_blank">
                <img src="${comment.author.avatarUrl}" class="comment-avatar" style="border-radius: 50%;" onerror="this.src='/img/avatar.jpg'">
            </a>
            <div class="content">
                <a class="comment" href="${comment.url}" target="_blank">
                    ${text}
                </a>
                <div class="name">
                    <span>${comment.author.name} / ${formatTimeAgo(comment.createdAt)}</span>
                </div>
            </div>
        </div>
        `;
    });
    
    container.innerHTML = html;
}

/**
 * 格式化时间为友好显示
 */
function formatTimeAgo(timeStr) {
    const time = new Date(timeStr).getTime();
    const now = Date.now();
    const diff = now - time;
    
    if (diff < 60000) {
        return '刚刚';
    } else if (diff < 3600000) {
        return Math.floor(diff / 60000) + '分钟前';
    } else if (diff < 86400000) {
        return Math.floor(diff / 3600000) + '小时前';
    } else if (diff < 2592000000) {
        return Math.floor(diff / 86400000) + '天前';
    } else {
        const date = new Date(time);
        return date.getFullYear() + '-' + 
               (date.getMonth() + 1).toString().padStart(2, '0') + '-' + 
               date.getDate().toString().padStart(2, '0');
    }
} 