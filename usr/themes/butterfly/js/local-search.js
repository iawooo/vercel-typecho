/**
 * 本地搜索功能
 * 支持XML输入数据并增强错误处理
 */

// 全局变量
let searchData = null;
let isSearchLoading = false;
let searchTimer = null;

// 配置
const CONFIG = {
  // 搜索数据路径 - 默认改为 search.xml
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
  try {
    const searchResultsContainer = document.getElementById('local-search-results');
    if (searchResultsContainer) {
      if (error) {
        console.error(message, error);
      }
      searchResultsContainer.innerHTML = `<div class="search-error"><p>${message}</p></div>`;
    }
  } catch (e) {
    console.error("显示搜索错误时发生异常:", e);
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
      console.warn('搜索对话框元素 #local-search 不存在');
      return; // 提前返回，防止后续错误
    }

    // 显示搜索对话框
    searchDialog.style.display = 'block';
    document.body.classList.add('search-active');

    // 聚焦搜索输入框
    const searchInput = document.getElementById('local-search-input');
    if (searchInput) {
      setTimeout(() => {
        try {
          searchInput.focus();
        } catch (focusError) {
          console.warn("聚焦搜索输入框时出错:", focusError);
        }
        
        // 加载搜索数据
        if (!searchData) {
          loadSearchData();
        }
      }, 100);
    } else {
        console.warn('搜索输入框元素 #local-search-input 不存在');
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
        console.warn('尝试关闭不存在的搜索对话框 #local-search');
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
    } else {
        console.warn('搜索状态元素 #search-status 不存在');
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
    if (!data || data.trim() === '') {
      throw new Error('接收到的搜索数据为空');
    }
    // 尝试解析XML
    if (data.trim().startsWith('<?xml') || data.trim().startsWith('<search>')) {
      return parseXMLData(data);
    }
    
    // 尝试解析JSON
    try {
      const jsonData = JSON.parse(data);
      if (!Array.isArray(jsonData)) {
        throw new Error('JSON数据不是数组格式');
      }
      return jsonData;
    } catch (jsonError) {
      throw new Error(`数据格式无效，既不是有效的XML也不是JSON: ${jsonError.message}`);
    }
  } catch (error) {
    console.error('解析搜索数据失败:', error);
    throw error; // 重新抛出错误，让调用者处理
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
      console.error('XML 解析错误详情:', parserError.textContent);
      throw new Error('XML解析错误');
    }
    
    const entries = xmlDoc.querySelectorAll('entry');
    if (entries.length === 0) {
      console.warn('XML数据中未找到 <entry> 元素');
      return []; // 返回空数组，而不是抛出错误
    }
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
    throw error; // 重新抛出错误
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
  
  try {
    const escapeRegex = str => str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const regex = new RegExp(escapeRegex(keyword), 'gi');
    return text.replace(regex, match => `<span class="search-keyword">${match}</span>`);
  } catch (error) {
    console.warn("高亮关键词时出错:", error);
    return text;
  }
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
  try {
    const lowerContent = content.toLowerCase();
    const lowerKeyword = keyword.toLowerCase();
    const keywordPosition = lowerContent.indexOf(lowerKeyword);
    
    if (keywordPosition < 0) {
        // 关键词不在内容中，返回内容开头部分
        return content.substring(0, Math.min(content.length, contentLength));
    }
    
    // 计算上下文的起始和结束位置
    let startPos = Math.max(0, keywordPosition - Math.floor(contentLength / 2));
    let endPos = Math.min(content.length, startPos + contentLength);
    
    // 尝试优化上下文的起始和结束边界，使其更自然
    // (这部分可以根据需要调整或移除)
    if (startPos > 0) {
      const spaceBefore = content.lastIndexOf(' ', startPos);
      if (spaceBefore !== -1) startPos = spaceBefore + 1;
    }
    if (endPos < content.length) {
        const spaceAfter = content.indexOf(' ', endPos);
        if (spaceAfter !== -1) endPos = spaceAfter;
    }
    
    // 提取上下文
    let result = content.substring(startPos, endPos);
    
    // 添加省略号
    if (startPos > 0) result = '... ' + result;
    if (endPos < content.length) result = result + ' ...';
    
    return result;
  } catch (error) {
    console.warn("提取上下文时出错:", error);
    // 出错时返回内容开头部分作为降级
    return content.substring(0, Math.min(content.length, contentLength));
  }
}

/**
 * 搜索函数
 * @param {string} keyword 搜索关键词
 */
function search(keyword) {
  try {
    const searchResults = document.getElementById('local-search-results');
    if (!searchResults) {
      console.error('搜索结果容器 #local-search-results 不存在');
      return;
    }

    if (!keyword) {
      searchResults.innerHTML = ''; // 清空结果
      return;
    }
    
    if (!searchData) {
      showSearchError('搜索数据正在加载或加载失败，请稍后重试。');
      // 尝试重新加载数据，以防第一次失败
      if (!isSearchLoading) loadSearchData(); 
      return;
    }

    if (searchData.length === 0) {
      showSearchError('无可用搜索数据。');
      return;
    }
    
    // 分割关键词（支持多关键词搜索）
    const keywords = keyword.trim().toLowerCase().split(/\s+/).filter(k => k !== ''); // 过滤空关键词
    if (keywords.length === 0) {
        searchResults.innerHTML = ''; // 如果只有空格，也清空结果
        return;
    }
    
    // 初始化结果HTML
    let resultHTML = '';
    let matchCount = 0;
    
    // 遍历所有数据项
    for (const item of searchData) {
      // 检查数据项是否有效
      if (!item || typeof item.title !== 'string' || typeof item.content !== 'string' || typeof item.url !== 'string') {
        console.warn('跳过无效的搜索数据项:', item);
        continue;
      }

      // 检查是否包含所有关键词
      const titleLower = item.title.toLowerCase();
      const contentLower = item.content.toLowerCase();
      
      const isMatch = keywords.every(key => titleLower.includes(key) || contentLower.includes(key));
      
      // 如果标题或内容包含所有关键词
      if (isMatch) {
        if (matchCount < CONFIG.maxResultLength) {
          let resultItemHTML = '<div class="search-result-item">';
          
          // 高亮标题
          let titleHighlighted = item.title;
          keywords.forEach(key => {
            titleHighlighted = highlightKeyword(titleHighlighted, key);
          });
          
          // 高亮内容
          // 查找第一个匹配的关键词以提取上下文
          const firstMatchedKeyword = keywords.find(key => contentLower.includes(key) || titleLower.includes(key));
          let contentHighlighted = extractContext(item.content, firstMatchedKeyword || keywords[0]); // 使用第一个匹配的或第一个关键词
          
          // 高亮所有关键词
          keywords.forEach(key => {
            contentHighlighted = highlightKeyword(contentHighlighted, key);
          });
          
          // 组装结果项
          resultItemHTML += `<a href="${item.url}" class="search-result-title">${titleHighlighted}</a>`;
          resultItemHTML += `<div class="search-result-content">${contentHighlighted}</div>`;
          resultItemHTML += '</div>';
          
          resultHTML += resultItemHTML;
        }
        matchCount++;
      }
    }
    
    // 显示结果
    if (matchCount > 0) {
      if (matchCount > CONFIG.maxResultLength) {
        resultHTML += `<div class="search-result-count">显示前 ${CONFIG.maxResultLength} 条结果，共找到 ${matchCount} 条</div>`;
      }
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
  let timeoutId = null; 
  return function(...args) {
    clearTimeout(timeoutId);
    const context = this;
    timeoutId = setTimeout(() => {
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
    } else {
      console.warn('搜索按钮 #search-button 未找到');
    }
    
    // 关闭按钮点击事件
    const closeButton = document.getElementById('search-close-button');
    if (closeButton) {
      closeButton.addEventListener('click', closeSearch);
    } else {
       console.warn('搜索关闭按钮 #search-close-button 未找到');
    }
    
    // 搜索遮罩点击事件
    const searchMask = document.getElementById('search-mask');
    if (searchMask) {
      searchMask.addEventListener('click', closeSearch);
    } else {
      console.warn('搜索遮罩 #search-mask 未找到');
    }
    
    // 搜索输入框输入事件
    const searchInput = document.getElementById('local-search-input');
    if (searchInput) {
      const debouncedSearch = debounce(function() {
        search(this.value.trim());
      }, 300);
      searchInput.addEventListener('input', debouncedSearch);
      
      // 回车键搜索
      searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault(); // 阻止表单默认提交行为
          search(this.value.trim());
        }
      });
    } else {
      console.warn('搜索输入框 #local-search-input 未找到');
    }
    
    console.log('本地搜索功能初始化成功');
  } catch (error) {
    console.error('初始化本地搜索功能时出错:', error);
    showSearchError('初始化本地搜索功能时出错，请刷新页面重试。', error);
  }
}

// 文档加载完成时初始化搜索功能
document.addEventListener('DOMContentLoaded', initLocalSearch);
