/**
 * 紧急修复点击事件问题的脚本
 * 采用激进方法修复整个页面无法点击的问题
 */

// 立即执行函数
(function() {
    console.log("执行紧急点击修复...");
    
    // 强制修复所有元素
    function forceClickable() {
        // 获取所有元素
        var allElements = document.getElementsByTagName('*');
        
        // 遍历并强制设置可点击
        for (var i = 0; i < allElements.length; i++) {
            var el = allElements[i];
            
            // 跳过特定元素，如遮罩
            if (el.id === 'search-mask' || el.id === 'menu-mask') {
                continue;
            }
            
            // 强制设置点击相关属性
            el.style.setProperty('pointer-events', 'auto', 'important');
            el.style.setProperty('user-select', 'auto', 'important');
            el.style.setProperty('touch-action', 'auto', 'important');
            
            // 移除事件监听器
            if (typeof el.onclick === 'function') {
                var originalClick = el.onclick;
                el.onclick = function(e) {
                    // 允许事件继续传播
                    e.stopPropagation = function() {};
                    return originalClick.apply(this, arguments);
                };
            }
        }
        
        // 特别强制设置body和html
        document.body.style.setProperty('pointer-events', 'auto', 'important');
        document.documentElement.style.setProperty('pointer-events', 'auto', 'important');
        document.body.style.setProperty('overflow', 'auto', 'important');
        document.body.style.setProperty('width', 'auto', 'important');
        document.body.style.setProperty('height', 'auto', 'important');
        
        console.log("已强制设置所有元素可点击");
    }
    
    // 立即执行
    forceClickable();
    
    // 移除可能的全屏覆盖层
    function removeOverlays() {
        // 查找固定定位的元素
        var fixedElements = document.querySelectorAll('[style*="position: fixed"], [style*="position:fixed"]');
        for (var i = 0; i < fixedElements.length; i++) {
            var el = fixedElements[i];
            
            // 保留搜索相关元素
            if (el.id === 'search-mask' || el.id === 'local-search' || 
                el.id === 'sidebar' || el.id === 'sidebar-menus' || 
                el.id === 'menu-mask') {
                continue;
            }
            
            // 检查是否覆盖整个屏幕
            var style = window.getComputedStyle(el);
            if ((style.top === '0px' || style.top === '0%') && 
                (style.left === '0px' || style.left === '0%') && 
                style.width.includes('100') && style.height.includes('100')) {
                
                // 移除覆盖层
                el.style.setProperty('display', 'none', 'important');
                el.style.setProperty('pointer-events', 'none', 'important');
                console.log("移除全屏覆盖层:", el.id || el.className);
            }
        }
    }
    
    // 立即移除覆盖层
    removeOverlays();
    
    // 劫持并修复事件
    function hijackEvents() {
        // 保存原始方法
        var originalAddEventListener = EventTarget.prototype.addEventListener;
        var originalRemoveEventListener = EventTarget.prototype.removeEventListener;
        
        // 替换addEventListener
        EventTarget.prototype.addEventListener = function(type, listener, options) {
            if (type === 'click' || type === 'touchstart' || type === 'touchmove' || 
                type === 'touchend' || type === 'mousedown' || type === 'mouseup') {
                
                // 包装监听器，确保点击事件正常传播
                var wrappedListener = function(e) {
                    // 阻止阻止默认行为
                    var originalPreventDefault = e.preventDefault;
                    e.preventDefault = function() {
                        // 对特定元素允许preventDefault
                        if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || 
                            e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || 
                            e.target.tagName === 'SELECT') {
                            originalPreventDefault.apply(this, arguments);
                        }
                    };
                    
                    // 调用原始监听器
                    return listener.apply(this, arguments);
                };
                
                // 调用原始方法
                return originalAddEventListener.call(this, type, wrappedListener, options);
            }
            
            // 调用原始方法
            return originalAddEventListener.call(this, type, listener, options);
        };
        
        console.log("已劫持事件方法");
    }
    
    // 劫持事件
    hijackEvents();
    
    // 启用链接和按钮
    function activateLinks() {
        // 获取所有链接和按钮
        var links = document.querySelectorAll('a, button, .site-page, .menus_item, input[type="submit"], input[type="button"]');
        
        // 为每个链接添加点击事件
        for (var i = 0; i < links.length; i++) {
            var link = links[i];
            
            // 添加点击事件
            link.addEventListener('click', function(e) {
                // 允许默认行为
                e.stopPropagation();
                return true;
            }, true);
            
            // 强制设置样式
            link.style.setProperty('pointer-events', 'auto', 'important');
            link.style.setProperty('cursor', 'pointer', 'important');
        }
        
        console.log("已激活所有链接和按钮");
    }
    
    // 激活链接和按钮
    activateLinks();
    
    // 修复jQuery事件（如果jQuery存在）
    function fixjQueryEvents() {
        if (typeof jQuery !== 'undefined') {
            // 移除可能的全局事件处理程序
            jQuery(document).off('click');
            jQuery(document).off('touchstart');
            jQuery(document).off('touchmove');
            jQuery(document).off('touchend');
            
            // 确保body可点击
            jQuery('body').css({
                'pointer-events': 'auto',
                'overflow': 'auto',
                'width': 'auto',
                'height': 'auto'
            });
            
            console.log("已修复jQuery事件");
        }
    }
    
    // 修复jQuery事件
    fixjQueryEvents();
    
    // 在内容加载后执行所有修复
    document.addEventListener('DOMContentLoaded', function() {
        forceClickable();
        removeOverlays();
        activateLinks();
        fixjQueryEvents();
        console.log("DOMContentLoaded: 再次执行所有修复");
    });
    
    // 在页面完全加载后执行所有修复
    window.addEventListener('load', function() {
        forceClickable();
        removeOverlays();
        activateLinks();
        fixjQueryEvents();
        console.log("页面加载完成: 再次执行所有修复");
        
        // 延迟执行，捕获可能的动态添加元素
        setTimeout(function() {
            forceClickable();
            removeOverlays();
            activateLinks();
            console.log("延迟修复完成");
        }, 1000);
    });
    
    // 定期检查并修复
    setInterval(function() {
        forceClickable();
        fixjQueryEvents();
    }, 3000);
    
    console.log("紧急点击修复已完成初始化");
})(); 
