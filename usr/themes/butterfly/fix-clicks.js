/**
 * 点击事件修复脚本
 * 用于解决页面点击无响应问题
 */

// 页面加载完成后执行修复
document.addEventListener('DOMContentLoaded', function() {
    console.log("执行点击事件修复...");
    
    // 移除所有可能的全局事件拦截
    const removeGlobalEventBlockers = function() {
        // 移除document上的事件拦截器
        document.removeEventListener('click', preventDefaultHandler, true);
        document.removeEventListener('touchstart', preventDefaultHandler, true);
        document.removeEventListener('touchmove', preventDefaultHandler, true);
        document.removeEventListener('touchend', preventDefaultHandler, true);
        document.removeEventListener('mousedown', preventDefaultHandler, true);
        document.removeEventListener('mouseup', preventDefaultHandler, true);
        
        // 移除body上的事件拦截器
        document.body.removeEventListener('click', preventDefaultHandler, true);
        document.body.removeEventListener('touchstart', preventDefaultHandler, true);
        document.body.removeEventListener('touchmove', preventDefaultHandler, true);
        document.body.removeEventListener('touchend', preventDefaultHandler, true);
        
        console.log("已移除全局事件拦截器");
    };
    
    // 用于移除的空函数
    function preventDefaultHandler(e) {
        e.preventDefault();
    }
    
    // 应用修复
    removeGlobalEventBlockers();
    
    // 确保所有链接和按钮可点击
    const enableClickableElements = function() {
        // 重要交互元素列表
        const clickableSelectors = [
            'a', 'button', 'input', 'textarea', 'select',
            '.site-page', '.menus_item', '.search-form-input',
            '.dark-light-com', '#nav a', '#menus a',
            '.recent-post-item', '.pagination a'
        ];
        
        // 应用可点击样式
        clickableSelectors.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            elements.forEach(el => {
                el.style.pointerEvents = 'auto';
                el.style.cursor = 'pointer';
                
                // 确保点击事件正常工作
                el.addEventListener('click', function(e) {
                    e.stopPropagation(); // 防止事件被上层拦截
                    return true; // 允许默认行为
                });
            });
        });
        
        console.log("已启用可点击元素");
    };
    
    enableClickableElements();
    
    // 修复body样式
    const fixBodyStyles = function() {
        // 重置body样式
        document.body.style.pointerEvents = 'auto';
        document.body.style.overflow = '';
        document.body.style.width = '';
        document.body.style.height = '';
        document.body.style.position = '';
        
        // 修复内容包装器
        const bodyWrap = document.getElementById('body-wrap');
        if (bodyWrap) {
            bodyWrap.style.pointerEvents = 'auto';
            bodyWrap.style.position = 'relative';
            bodyWrap.style.zIndex = '1';
        }
        
        console.log("已修复body样式");
    };
    
    fixBodyStyles();
    
    // 移除可能的全屏覆盖层
    const removeOverlays = function() {
        // 查找可能的覆盖层
        const overlays = document.querySelectorAll('.overlay, .mask, [style*="position: fixed"]');
        overlays.forEach(overlay => {
            // 保留必要的固定元素
            if (!overlay.id.includes('search') && 
                !overlay.id.includes('menu') && 
                !overlay.id.includes('sidebar')) {
                overlay.style.display = 'none';
                overlay.style.pointerEvents = 'none';
                console.log("已移除覆盖层:", overlay.id || overlay.className);
            }
        });
    };
    
    removeOverlays();
    
    // 修复touchstart事件
    document.addEventListener('touchstart', function() {
        // 空函数，确保touchstart事件正常工作
    }, { passive: true });
    
    console.log("点击事件修复完成");
});

// 页面完全加载后再次执行修复（包括图片和其他资源）
window.addEventListener('load', function() {
    // 确保页面完全加载后依然可点击
    document.body.style.pointerEvents = 'auto';
    
    // 尝试查找并修复可能的问题元素
    setTimeout(function() {
        const elements = document.querySelectorAll('*');
        elements.forEach(el => {
            const computedStyle = window.getComputedStyle(el);
            if (computedStyle.pointerEvents === 'none') {
                // 跳过搜索和菜单相关遮罩
                if (el.id !== 'search-mask' && el.id !== 'menu-mask') {
                    el.style.pointerEvents = 'auto';
                }
            }
        });
        
        console.log("页面完全加载后修复完成");
    }, 1000); // 延迟1秒执行，确保所有动态内容都已加载
}); 
