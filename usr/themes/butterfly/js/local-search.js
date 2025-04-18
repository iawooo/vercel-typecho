/**
 * 增强版本地搜索功能
 * 提供更好的错误处理和数据格式兼容性
 */

// 全局变量
let searchData = null;
let isSearchLoading = false;
let searchTimer = null;

// 配置
const CONFIG = {
  // 搜索数据路径
  path: (document.getElementById('search-path') || { dataset: { path: '/search.xml' } }).dataset.path,
  // 内容长度
  contentLength: 380,
  // 最大结果数
  maxResultLength: 10
};

/**
 * 显示错误信息
 * @param {string} message 错误信息
 * @param {Error|null} error 错误对象
 */
function showSearchError(message, error = null) {
  const searchResultsContainer = document.getElementById('local-search-results');
  if (searchResultsContainer) {
    if (error) {
      console.error(message, error);
    }
    searchResultsContainer.innerHTML = `<div class="search-error"><p>${message}</p></div>`;
  }
}

/**
 * 打开搜索对话框
 */
function openSearch() {
  try {
    // 获取搜索对话框
    const searchDialog = document.getElementById('local-search');
    if (!searchDialog) {
      throw new Error('搜索对话框不存在');
    }

    // 显示搜索对话框
    searchDialog.style.display = 'block';
    document.body.classList.add('search-active');

    // 聚焦搜索输入框
    const searchInput = document.getElementById('local-search-input');
    if (searchInput) {
      setTimeout(() => {
        searchInput.focus();
        
        // 加载搜索数据
        if (!searchData) {
          loadSearchData();
        }
      }, 100);
    }

    // 添加ESC关闭事件
    document.addEventListener('keydown', handleEscKeyClose);
  } catch (error) {
    console.error('打开搜索对话框时出错:', error);
    showSearchError('打开搜索对话框时出错，请刷新页面重试。', error);
  }
}

/**
 * 处理ESC键关闭
 * @param {KeyboardEvent} event 键盘事件
 */
function handleEscKeyClose(event) {
  if (event.key === 'Escape' || event.keyCode === 27) {
    closeSearch();
  }
}

/**
 * 关闭搜索对话框
 */
function closeSearch() {
  try {
    // 获取搜索对话框
    const searchDialog = document.getElementById('local-search');
    if (!searchDialog) {
      return;
    }

    // 隐藏搜索对话框
    searchDialog.style.display = 'none';
    document.body.classList.remove('search-active');

    // 清空搜索输入和结果
    const searchInput = document.getElementById('local-search-input');
    if (searchInput) {
      searchInput.value = '';
    }
    const searchResults = document.getElementById('local-search-results');
    if (searchResults) {
      searchResults.innerHTML = '';
    }

    // 移除ESC关闭事件
    document.removeEventListener('keydown', handleEscKeyClose);
  } catch (error) {
    console.error('关闭搜索对话框时出错:', error);
  }
}

/**
 * 加载搜索数据
 */
function loadSearchData() {
  try {
    // 避免重复加载
    if (isSearchLoading || searchData) {
      return;
    }

    isSearchLoading = true;
    const searchStatus = document.getElementById('search-status');
    if (searchStatus) {
      searchStatus.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 加载数据中...';
    }

    // 获取搜索路径
    const searchPath = CONFIG.path;
    if (!searchPath) {
      throw new Error('搜索路径未配置');
    }

    // 请求搜索数据
    fetch(searchPath)
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP 错误: ${response.status}`);
        }
        return response.text();
      })
      .then(data => {
        try {
          searchData = parseSearchData(data);
          if (searchStatus) {
            searchStatus.innerHTML = '';
          }
          
          // 如果有输入内容，立即搜索
          const searchInput = document.getElementById('local-search-input');
          if (searchInput && searchInput.value.trim() !== '') {
            search(searchInput.value.trim());
          }
        } catch (parseError) {
          throw new Error(`解析搜索数据失败: ${parseError.message}`);
        }
      })
      .catch(error => {
        console.error('获取搜索数据失败:', error);
        showSearchError(`获取搜索数据失败: ${error.message}`);
        if (searchStatus) {
          searchStatus.innerHTML = '<i class="fas fa-exclamation-circle"></i> 数据加载失败';
        }
      })
      .finally(() => {
        isSearchLoading = false;
      });
  } catch (error) {
    console.error('加载搜索数据时出错:', error);
    showSearchError('加载搜索数据时出错，请刷新页面重试。', error);
    isSearchLoading = false;
  }
}

/**
 * 解析搜索数据
 * @param {string} data XML或JSON格式的搜索数据
 * @returns {Array} 搜索数据数组
 */
function parseSearchData(data) {
  try {
    // 尝试解析XML
    if (data.trim().startsWith('<?xml') || data.trim().startsWith('<search>')) {
      return parseXMLData(data);
    }
    
    // 尝试解析JSON
    try {
      return JSON.parse(data);
    } catch (jsonError) {
      throw new Error(`数据格式无效: ${jsonError.message}`);
    }
  } catch (error) {
    console.error('解析搜索数据失败:', error);
    throw error;
  }
}

/**
 * 解析XML搜索数据
 * @param {string} xmlData XML格式的搜索数据
 * @returns {Array} 搜索数据数组
 */
function parseXMLData(xmlData) {
  try {
    const parser = new DOMParser();
    const xmlDoc = parser.parseFromString(xmlData, 'text/xml');
    
    // 检查解析错误
    const parserError = xmlDoc.querySelector('parsererror');
    if (parserError) {
      throw new Error('XML解析错误: ' + parserError.textContent);
    }
    
    const entries = xmlDoc.querySelectorAll('entry');
    const results = [];
    
    entries.forEach(entry => {
      const result = {};
      
      // 获取所有子元素
      Array.from(entry.children).forEach(element => {
        const tagName = element.tagName.toLowerCase();
        result[tagName] = element.textContent;
      });
      
      // 确保必要字段存在
      if (!result.title) result.title = '无标题';
      if (!result.content) result.content = '';
      if (!result.url) result.url = '#';
      
      results.push(result);
    });
    
    return results;
  } catch (error) {
    console.error('解析XML搜索数据失败:', error);
    throw error;
  }
}

/**
 * 高亮关键词
 * @param {string} text 文本内容
 * @param {string} keyword 关键词
 * @returns {string} 高亮后的HTML
 */
function highlightKeyword(text, keyword) {
  if (!keyword || !text) return text;
  
  const escapeRegex = str => str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  const regex = new RegExp(escapeRegex(keyword), 'gi');
  
  return text.replace(regex, match => `<span class="search-keyword">${match}</span>`);
}

/**
 * 提取上下文
 * @param {string} content 完整内容
 * @param {string} keyword 关键词
 * @returns {string} 包含关键词的上下文
 */
function extractContext(content, keyword) {
  if (!keyword || !content) return '';
  
  const contentLength = CONFIG.contentLength;
  const keywordPosition = content.toLowerCase().indexOf(keyword.toLowerCase());
  
  if (keywordPosition < 0) return content.substring(0, contentLength);
  
  // 计算上下文的起始和结束位置
  let startPos = Math.max(0, keywordPosition - Math.floor(contentLength / 2));
  let endPos = Math.min(content.length, startPos + contentLength);
  
  // 确保显示完整单词
  if (startPos > 0) {
    startPos = content.indexOf(' ', startPos);
    if (startPos === -1) startPos = 0;
  }
  
  // 添加省略号
  let result = content.substring(startPos, endPos);
  if (startPos > 0) result = '... ' + result;
  if (endPos < content.length) result = result + ' ...';
  
  return result;
}

/**
 * 搜索函数
 * @param {string} keyword 搜索关键词
 */
function search(keyword) {
  try {
    if (!keyword) {
      const searchResults = document.getElementById('local-search-results');
      if (searchResults) {
        searchResults.innerHTML = '';
      }
      return;
    }
    
    if (!searchData) {
      loadSearchData();
      return;
    }
    
    // 分割关键词（支持多关键词搜索）
    const keywords = keyword.trim().toLowerCase().split(/\s+/);
    const searchResults = document.getElementById('local-search-results');
    
    if (!searchResults) {
      throw new Error('搜索结果容器不存在');
    }
    
    // 初始化结果HTML
    let resultHTML = '';
    let matchCount = 0;
    
    // 遍历所有数据项
    for (const item of searchData) {
      // 检查是否包含所有关键词
      const matchTitle = keywords.every(key => item.title && item.title.toLowerCase().indexOf(key) > -1);
      const matchContent = keywords.every(key => item.content && item.content.toLowerCase().indexOf(key) > -1);
      
      // 如果标题或内容包含关键词
      if (matchTitle || matchContent) {
        matchCount++;
        let resultItemHTML = '<div class="search-result-item">';
        
        // 高亮标题
        let titleHighlighted = item.title;
        keywords.forEach(key => {
          titleHighlighted = highlightKeyword(titleHighlighted, key);
        });
        
        // 高亮内容
        let contentHighlighted = '';
        if (matchContent) {
          // 使用第一个匹配的关键词提取上下文
          const firstMatchedKeyword = keywords.find(key => item.content.toLowerCase().indexOf(key) > -1);
          const context = extractContext(item.content, firstMatchedKeyword);
          
          // 高亮所有关键词
          contentHighlighted = context;
          keywords.forEach(key => {
            contentHighlighted = highlightKeyword(contentHighlighted, key);
          });
        } else {
          contentHighlighted = item.content.substring(0, CONFIG.contentLength);
          if (contentHighlighted.length === CONFIG.contentLength) {
            contentHighlighted += '...';
          }
        }
        
        // 组装结果项
        resultItemHTML += `<a href="${item.url}" class="search-result-title">${titleHighlighted}</a>`;
        resultItemHTML += `<div class="search-result-content">${contentHighlighted}</div>`;
        resultItemHTML += '</div>';
        
        resultHTML += resultItemHTML;
        
        // 限制结果数量
        if (matchCount >= CONFIG.maxResultLength) {
          break;
        }
      }
    }
    
    // 显示结果
    if (matchCount > 0) {
      searchResults.innerHTML = resultHTML;
    } else {
      searchResults.innerHTML = '<div class="search-empty"><p>未找到相关结果，请尝试其他关键词</p></div>';
    }
  } catch (error) {
    console.error('搜索时出错:', error);
    showSearchError('搜索时出错: ' + error.message, error);
  }
}

/**
 * 防抖函数
 * @param {Function} func 要执行的函数
 * @param {number} delay 延迟时间，单位毫秒
 * @returns {Function} 防抖后的函数
 */
function debounce(func, delay) {
  return function() {
    const context = this;
    const args = arguments;
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
      func.apply(context, args);
    }, delay);
  };
}

/**
 * 初始化搜索功能
 */
function initLocalSearch() {
  try {
    // 搜索按钮点击事件
    const searchButton = document.getElementById('search-button');
    if (searchButton) {
      searchButton.addEventListener('click', openSearch);
    }
    
    // 关闭按钮点击事件
    const closeButton = document.getElementById('search-close-button');
    if (closeButton) {
      closeButton.addEventListener('click', closeSearch);
    }
    
    // 搜索遮罩点击事件
    const searchMask = document.getElementById('search-mask');
    if (searchMask) {
      searchMask.addEventListener('click', closeSearch);
    }
    
    // 搜索输入框输入事件
    const searchInput = document.getElementById('local-search-input');
    if (searchInput) {
      searchInput.addEventListener('input', debounce(function() {
        search(this.value.trim());
      }, 300));
      
      // 回车键搜索
      searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
          search(this.value.trim());
        }
      });
    }
    
    console.log('本地搜索功能初始化成功');
  } catch (error) {
    console.error('初始化本地搜索功能时出错:', error);
    showSearchError('初始化本地搜索功能时出错，请刷新页面重试。', error);
  }
}

// 文档加载完成时初始化搜索功能
document.addEventListener('DOMContentLoaded', initLocalSearch);
