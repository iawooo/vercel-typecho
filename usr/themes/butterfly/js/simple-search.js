// 简单搜索功能实现
document.addEventListener('DOMContentLoaded', function() {
  console.log('初始化简化版搜索功能...');
  
  try {
    // 获取搜索相关元素
    const searchButton = document.getElementById('search-button');
    const searchContainer = document.getElementById('local-search');
    const searchDialog = document.querySelector('#local-search .search-dialog');
    const searchMask = document.getElementById('search-mask');
    const closeButton = document.querySelector('#local-search .search-close-button');
    const searchInput = document.querySelector('#local-search-input input');
    const searchResults = document.getElementById('local-search-results');
    
    // 检查必要元素是否存在
    if (!searchButton) {
      console.error('搜索按钮不存在，ID: search-button');
      return;
    }
    
    if (!searchContainer) {
      console.error('搜索容器不存在，ID: local-search');
      return;
    }
    
    if (!searchDialog) {
      console.error('搜索对话框不存在，选择器: #local-search .search-dialog');
      return;
    }
    
    if (!searchMask) {
      console.error('搜索遮罩不存在，ID: search-mask');
      return;
    }
    
    // 打开搜索对话框
    function openSearch() {
      try {
        console.log('打开搜索对话框');
        document.body.style.overflow = 'hidden';
        searchContainer.style.display = 'block';
        searchMask.style.display = 'block';
        searchDialog.style.display = 'block';
        
        // 聚焦输入框
        if (searchInput) {
          setTimeout(function() {
            searchInput.focus();
          }, 300);
        }
      } catch (err) {
        console.error('打开搜索对话框失败:', err);
      }
    }
    
    // 关闭搜索对话框
    function closeSearch() {
      try {
        console.log('关闭搜索对话框');
        document.body.style.overflow = '';
        searchContainer.style.display = 'none';
        searchMask.style.display = 'none';
        searchDialog.style.display = 'none';
      } catch (err) {
        console.error('关闭搜索对话框失败:', err);
      }
    }
    
    // 绑定搜索按钮点击事件
    console.log('添加搜索按钮点击事件');
    searchButton.addEventListener('click', openSearch);
    
    // 绑定关闭按钮点击事件
    if (closeButton) {
      closeButton.addEventListener('click', closeSearch);
    } else {
      console.warn('搜索关闭按钮不存在，选择器: #local-search .search-close-button');
    }
    
    // 绑定遮罩点击事件
    searchMask.addEventListener('click', closeSearch);
    
    // 处理搜索表单提交
    if (searchInput) {
      const searchForm = searchInput.closest('form');
      if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
          const query = searchInput.value.trim();
          if (query === '') {
            e.preventDefault();
            console.log('空查询被阻止提交');
          } else {
            console.log('提交搜索查询:', query);
          }
        });
      } else {
        console.warn('找不到包含搜索输入框的表单');
      }
    } else {
      console.warn('搜索输入框不存在，选择器: #local-search-input input');
    }
    
    // 添加ESC键关闭搜索
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && searchContainer.style.display === 'block') {
        closeSearch();
      }
    });
    
    console.log('简化版搜索功能初始化完成');
  } catch (err) {
    console.error('搜索功能初始化失败:', err);
  }
}); 
