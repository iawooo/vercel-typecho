/**
 * 高质量的本地搜索功能
 * 支持XML格式的输入数据
 * 包含完善的错误处理
 */

// 全局变量存储搜索数据和状态
let searchData = null;
let isLoading = false;
let searchTimer = null;

// 配置
const config = {
  // 默认搜索路径，可在页面中通过data-config属性覆盖
  searchPath: '/search.xml',
  // 默认每个结果显示的内容长度
  contentLength: 380,
  // 搜索结果最大数量
  maxResultCount: 10
};

/**
 * 显示错误消息
 * @param {string} message - 错误消息
 */
function showError(message) {
  const resultContainer = document.getElementById('local-search-results');
  if (resultContainer) {
    resultContainer.innerHTML = `<div class="search-error">${message}</div>`;
  }
  const loadingStatus = document.getElementById('loading-status');
  if (loadingStatus) {
    loadingStatus.style.display = 'none';
  }
}

/**
 * 打开搜索对话框
 */
function openSearch() {
  try {
    const searchDialog = document.getElementById('local-search');
    if (!searchDialog) {
      throw new Error('搜索对话框元素不存在');
    }
    
    searchDialog.style.display = 'block';
    document.body.style.overflow = 'hidden';  // 禁止滚动背景
    
    const searchInput = document.getElementById('local-search-input');
    if (searchInput) {
      searchInput.focus();
      
      // 如果数据尚未加载，则加载搜索数据
      if (!searchData && !isLoading) {
        loadSearchData();
      }
    }
  } catch (error) {
    console.error('打开搜索对话框时出错:', error);
    showError('打开搜索对话框失败: ' + error.message);
  }
}

/**
 * 关闭搜索对话框
 */
function closeSearch() {
  try {
    const searchDialog = document.getElementById('local-search');
    if (searchDialog) {
      searchDialog.style.display = 'none';
      document.body.style.overflow = '';  // 恢复滚动
    }
    
    // 清空搜索内容
    const searchInput = document.getElementById('local-search-input');
    if (searchInput) {
      searchInput.value = '';
    }
    
    // 清空搜索结果
    const searchResults = document.getElementById('local-search-results');
    if (searchResults) {
      searchResults.innerHTML = '';
    }
  } catch (error) {
    console.error('关闭搜索对话框时出错:', error);
  }
}

/**
 * 加载搜索数据
 */
function loadSearchData() {
  try {
    if (isLoading) return;
    isLoading = true;
    
    // 显示加载状态
    const loadingStatus = document.getElementById('loading-status');
    if (loadingStatus) {
      loadingStatus.style.display = 'block';
    }
    
    // 从配置元素获取搜索路径
    const searchConfig = document.getElementById('search-config');
    const searchPath = searchConfig && searchConfig.dataset.path ? 
      searchConfig.dataset.path : config.searchPath;
    
    // 请求搜索数据
    fetch(searchPath)
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP 错误: ${response.status}`);
        }
        return response.text();
      })
      .then(xmlText => {
        parseSearchData(xmlText);
        
        // 隐藏加载状态
        if (loadingStatus) {
          loadingStatus.style.display = 'none';
        }
        isLoading = false;
      })
      .catch(error => {
        console.error('加载搜索数据出错:', error);
        showError(`加载搜索数据失败: ${error.message}`);
        isLoading = false;
      });
  } catch (error) {
    console.error('加载搜索数据时出错:', error);
    showError('加载搜索数据失败: ' + error.message);
    isLoading = false;
  }
}

/**
 * 解析XML格式的搜索数据
 * @param {string} xmlText - XML文本
 */
function parseSearchData(xmlText) {
  try {
    if (!xmlText) {
      throw new Error('搜索数据为空');
    }
    
    // 解析XML
    const parser = new DOMParser();
    const xmlDoc = parser.parseFromString(xmlText, 'text/xml');
    
    // 检查解析错误
    const parseError = xmlDoc.querySelector('parsererror');
    if (parseError) {
      throw new Error('XML解析错误: ' + parseError.textContent);
    }
    
    // 提取所有条目
    const entries = xmlDoc.querySelectorAll('entry');
    if (!entries || entries.length === 0) {
      throw new Error('搜索数据中没有找到条目');
    }
    
    // 构建搜索数据结构
    searchData = [];
    entries.forEach(entry => {
      try {
        // 提取字段
        const title = entry.querySelector('title')?.textContent || '';
        const content = entry.querySelector('content')?.textContent || '';
        const url = entry.querySelector('url')?.textContent || '';
        
        if (title && url) {
          searchData.push({
            title: title,
            content: content,
            url: url
          });
        }
      } catch (entryError) {
        console.warn('解析条目时出错:', entryError);
      }
    });
    
    if (searchData.length === 0) {
      throw new Error('没有有效的搜索条目');
    }
    
    console.log(`成功加载 ${searchData.length} 条搜索记录`);
  } catch (error) {
    console.error('解析搜索数据时出错:', error);
    showError('解析搜索数据失败: ' + error.message);
    searchData = null;
  }
}

/**
 * 高亮匹配的关键词
 * @param {string} text - 原始文本
 * @param {string} keyword - 关键词
 * @return {string} 高亮后的HTML
 */
function highlightKeyword(text, keyword) {
  if (!text || !keyword) return text;
  
  try {
    const escapeRegExp = (str) => str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const regex = new RegExp('(' + escapeRegExp(keyword) + ')', 'gi');
    return text.replace(regex, '<span class="search-keyword">$1</span>');
  } catch (error) {
    console.warn('高亮关键词时出错:', error);
    return text;
  }
}

/**
 * 提取匹配关键词的上下文
 * @param {string} content - 完整内容
 * @param {string} keyword - 关键词
 * @return {string} 提取的上下文
 */
function extractContext(content, keyword) {
  if (!content || !keyword) return '';
  
  try {
    const contentLength = config.contentLength;
    const lowerContent = content.toLowerCase();
    const lowerKeyword = keyword.toLowerCase();
    const index = lowerContent.indexOf(lowerKeyword);
    
    if (index === -1) {
      // 如果找不到关键词，返回内容的前一部分
      return content.substring(0, contentLength);
    }
    
    // 计算上下文的起始和结束位置
    let start = Math.max(0, index - contentLength / 2);
    let end = Math.min(content.length, index + keyword.length + contentLength / 2);
    
    // 如果内容较短，直接返回全部
    if (content.length <= contentLength) {
      return content;
    }
    
    // 调整起止位置到单词边界
    while (start > 0 && content[start] !== ' ' && content[start] !== '\n') {
      start--;
    }
    
    while (end < content.length && content[end] !== ' ' && content[end] !== '\n') {
      end++;
    }
    
    // 提取上下文
    let extractedContent = content.substring(start, end);
    
    // 添加省略号
    if (start > 0) extractedContent = '...' + extractedContent;
    if (end < content.length) extractedContent = extractedContent + '...';
    
    return extractedContent;
  } catch (error) {
    console.warn('提取上下文时出错:', error);
    return content.substring(0, Math.min(content.length, config.contentLength)) + '...';
  }
}

/**
 * 搜索函数
 * @param {string} keyword - 搜索关键词
 */
function search(keyword) {
  try {
    if (!keyword) {
      // 如果关键词为空，清空结果
      const searchResults = document.getElementById('local-search-results');
      if (searchResults) {
        searchResults.innerHTML = '';
      }
      return;
    }
    
    if (!searchData) {
      showError('搜索数据尚未加载完成，请稍后再试');
      return;
    }
    
    // 分割关键词
    const keywords = keyword.trim().toLowerCase().split(/\s+/);
    if (keywords.length === 0) return;
    
    // 搜索匹配项
    const matches = [];
    searchData.forEach(data => {
      // 检查每个关键词是否在标题或内容中出现
      const titleLower = data.title.toLowerCase();
      const contentLower = data.content.toLowerCase();
      
      let matched = true;
      for (const kw of keywords) {
        if (titleLower.indexOf(kw) === -1 && contentLower.indexOf(kw) === -1) {
          matched = false;
          break;
        }
      }
      
      if (matched) {
        matches.push(data);
      }
    });
    
    // 显示结果
    const searchResults = document.getElementById('local-search-results');
    if (!searchResults) {
      throw new Error('搜索结果容器不存在');
    }
    
    if (matches.length === 0) {
      searchResults.innerHTML = '<div class="search-result-empty">没有找到相关结果</div>';
      return;
    }
    
    // 限制结果数量
    const limitedMatches = matches.slice(0, config.maxResultCount);
    
    // 生成结果HTML
    let html = '<ul class="search-result-list">';
    for (const match of limitedMatches) {
      // 提取上下文并高亮关键词
      let context = extractContext(match.content, keywords[0]);
      
      // 为每个关键词添加高亮
      let highlightedTitle = match.title;
      let highlightedContext = context;
      
      for (const kw of keywords) {
        highlightedTitle = highlightKeyword(highlightedTitle, kw);
        highlightedContext = highlightKeyword(highlightedContext, kw);
      }
      
      html += `
        <li class="search-result-item">
          <a href="${match.url}" class="search-result-title">${highlightedTitle}</a>
          <div class="search-result-content">${highlightedContext}</div>
        </li>
      `;
    }
    html += '</ul>';
    
    // 添加结果计数
    if (matches.length > config.maxResultCount) {
      html += `<div class="search-result-count">
        显示前 ${config.maxResultCount} 条结果，共找到 ${matches.length} 条
      </div>`;
    }
    
    searchResults.innerHTML = html;
  } catch (error) {
    console.error('搜索时出错:', error);
    showError('搜索失败: ' + error.message);
  }
}

/**
 * 防抖函数
 * @param {Function} func - 要执行的函数
 * @param {number} wait - 等待时间（毫秒）
 * @return {Function} 防抖后的函数
 */
function debounce(func, wait) {
  let timeout;
  return function(...args) {
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, args), wait);
  };
}

/**
 * 初始化搜索功能
 */
function initSearch() {
  try {
    // 初始化搜索按钮
    const searchButton = document.getElementById('search-button');
    if (searchButton) {
      searchButton.addEventListener('click', openSearch);
    }
    
    // 初始化搜索关闭按钮
    const closeButton = document.querySelector('.search-close-button');
    if (closeButton) {
      closeButton.addEventListener('click', closeSearch);
    }
    
    // 点击遮罩关闭搜索
    const searchMask = document.querySelector('.search-mask');
    if (searchMask) {
      searchMask.addEventListener('click', closeSearch);
    }
    
    // 阻止点击对话框时关闭
    const searchDialog = document.querySelector('.search-dialog');
    if (searchDialog) {
      searchDialog.addEventListener('click', function(e) {
        e.stopPropagation();
      });
    }
    
    // 搜索输入框事件
    const searchInput = document.getElementById('local-search-input');
    if (searchInput) {
      // 使用防抖处理输入
      const debouncedSearch = debounce((e) => {
        search(e.target.value);
      }, 300);
      
      searchInput.addEventListener('input', debouncedSearch);
      
      // 回车键触发搜索
      searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
          search(e.target.value);
        }
      });
    }
    
    // ESC键关闭搜索
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeSearch();
      }
    });
    
    console.log('搜索功能初始化完成');
  } catch (error) {
    console.error('初始化搜索功能时出错:', error);
  }
}

// 页面加载完成后初始化搜索功能
document.addEventListener('DOMContentLoaded', initSearch); 
