// 简单搜索功能实现
document.addEventListener('DOMContentLoaded', function() {
  // 获取搜索相关元素
  const searchButton = document.getElementById('search-button');
  const searchDialog = document.querySelector('#local-search .search-dialog');
  const searchMask = document.getElementById('search-mask');
  const closeButton = document.querySelector('#local-search .search-close-button');
  const searchInput = document.querySelector('#local-search-input input');
  const searchResults = document.getElementById('local-search-results');
  
  // 如果元素不存在，直接返回
  if (!searchButton || !searchDialog || !searchMask) {
    console.log('搜索所需的DOM元素不存在，搜索功能无法初始化');
    return;
  }
  
  // 打开搜索对话框
  function openSearch() {
    document.body.style.overflow = 'hidden';
    searchMask.style.display = 'block';
    searchDialog.style.display = 'block';
    setTimeout(() => {
      if (searchInput) searchInput.focus();
    }, 100);
  }
  
  // 关闭搜索对话框
  function closeSearch() {
    document.body.style.overflow = '';
    searchMask.style.display = 'none';
    searchDialog.style.display = 'none';
  }
  
  // 绑定搜索按钮点击事件
  searchButton.addEventListener('click', openSearch);
  
  // 绑定关闭按钮点击事件
  if (closeButton) {
    closeButton.addEventListener('click', closeSearch);
  }
  
  // 绑定遮罩点击事件
  searchMask.addEventListener('click', closeSearch);
  
  // 处理搜索表单提交
  if (searchInput) {
    const searchForm = searchInput.closest('form');
    if (searchForm) {
      searchForm.addEventListener('submit', function(e) {
        if (searchInput.value.trim() === '') {
          e.preventDefault();
        }
      });
    }
    
    // 如果需要实现不提交表单的搜索预览，可以添加以下代码
    // searchInput.addEventListener('input', function() {
    //   // 根据输入内容进行搜索逻辑...
    // });
  }
});
