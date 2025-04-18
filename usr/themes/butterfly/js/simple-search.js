// 简单搜索功能实现 - 只在footer.php没有内联搜索脚本时使用
document.addEventListener('DOMContentLoaded', function() {
  // 检查是否已有内联搜索脚本初始化
  if (window.searchInitialized) {
    console.log('内联搜索脚本已初始化，跳过simple-search.js');
    return;
  }
  
  console.log('初始化简化版搜索功能(simple-search.js)...');
  window.searchInitialized = true;
  
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
        
        // 加载搜索数据（如果已经实现）
        if (typeof window.loadLocalSearch === 'function' && !window.localSearchLoaded) {
          window.loadLocalSearch();
        } else {
          // 简单搜索实现
          if (!window.localSearchLoaded) {
            loadSearchData();
          }
        }
      } catch (err) {
        console.error('打开搜索对话框失败:', err);
      }
    }
    
    // 加载搜索数据
    function loadSearchData() {
      const path = 'search.xml';
      console.log('加载搜索数据:', path);
      
      const loadingElem = document.getElementById('loading-database');
      if (loadingElem) loadingElem.style.display = 'block';
      
      fetch(path)
        .then(response => {
          if (!response.ok) {
            throw new Error(`搜索索引加载失败: ${response.status}`);
          }
          return response.text();
        })
        .then(text => {
          console.log('搜索数据加载完成');
          window.searchData = text;
          window.localSearchLoaded = true;
          
          if (loadingElem) loadingElem.style.display = 'none';
          
          // 为搜索输入框添加事件监听器
          if (searchInput) {
            setupSearchInput();
          }
        })
        .catch(error => {
          console.error('加载搜索数据出错:', error);
          if (searchResults) {
            searchResults.innerHTML = `<div class="search-result-list"><div>搜索索引加载失败: ${error.message}</div></div>`;
          }
          if (loadingElem) loadingElem.style.display = 'none';
        });
    }
    
    // 设置搜索输入框处理
    function setupSearchInput() {
      searchInput.addEventListener('input', handleSearch);
    }
    
    // 处理搜索
    function handleSearch() {
      const keyword = searchInput.value.trim().toLowerCase();
      if (!keyword) {
        if (searchResults) searchResults.innerHTML = '';
        return;
      }
      
      try {
        // 使用DOMParser解析XML数据
        const parser = new DOMParser();
        const doc = parser.parseFromString(window.searchData, 'text/xml');
        
        // 处理解析错误
        if (doc.getElementsByTagName('parsererror').length > 0) {
          searchResults.innerHTML = '<div class="search-result-list"><div>搜索索引格式错误</div></div>';
          return;
        }
        
        // 执行搜索
        const entries = doc.getElementsByTagName('entry');
        const matchedEntries = [];
        
        for (let i = 0; i < entries.length; i++) {
          const entry = entries[i];
          const title = entry.querySelector('title')?.textContent.toLowerCase() || '';
          const content = entry.querySelector('content')?.textContent.toLowerCase() || '';
          const url = entry.querySelector('url')?.textContent || '/';
          
          if (title.includes(keyword) || content.includes(keyword)) {
            matchedEntries.push({ title, content, url });
          }
        }
        
        // 显示结果
        displayResults(matchedEntries, keyword);
      } catch (err) {
        console.error('搜索处理出错:', err);
        searchResults.innerHTML = '<div class="search-result-list"><div>搜索处理出错</div></div>';
      }
    }
    
    // 显示搜索结果
    function displayResults(results, keyword) {
      if (!searchResults) return;
      
      if (results.length === 0) {
        searchResults.innerHTML = `<div class="search-result-list"><div>找不到与"${keyword}"相关的内容</div></div>`;
        return;
      }
      
      let html = '<div class="search-result-list">';
      
      // 最多显示10条结果
      const maxResults = Math.min(results.length, 10);
      
      for (let i = 0; i < maxResults; i++) {
        const result = results[i];
        
        // 高亮关键词
        const highlightedTitle = result.title.replace(new RegExp(keyword, 'gi'), 
                                               match => `<span class="search-keyword">${match}</span>`);
        
        // 提取包含关键词的内容片段
        let contentSnippet = '';
        const keywordIndex = result.content.indexOf(keyword);
        
        if (keywordIndex !== -1) {
          const start = Math.max(0, keywordIndex - 30);
          const end = Math.min(result.content.length, keywordIndex + keyword.length + 70);
          contentSnippet = result.content.substring(start, end);
          
          if (start > 0) contentSnippet = '...' + contentSnippet;
          if (end < result.content.length) contentSnippet += '...';
          
          // 高亮关键词
          contentSnippet = contentSnippet.replace(new RegExp(keyword, 'gi'), 
                                          match => `<span class="search-keyword">${match}</span>`);
        } else {
          // 如果找不到关键词（可能是标题匹配），显示前100个字符
          contentSnippet = result.content.substring(0, 100) + '...';
        }
        
        html += `
          <div class="local-search__hit-item">
            <a href="${result.url}" class="search-result-title">${highlightedTitle}</a>
            <p class="search-result">${contentSnippet}</p>
          </div>
        `;
      }
      
      html += '</div>';
      searchResults.innerHTML = html;
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
    
    // 绑定事件
    console.log('添加搜索按钮点击事件');
    searchButton.addEventListener('click', openSearch);
    
    if (closeButton) {
      closeButton.addEventListener('click', closeSearch);
    } else {
      console.warn('搜索关闭按钮不存在，选择器: #local-search .search-close-button');
    }
    
    searchMask.addEventListener('click', closeSearch);
    
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
