window.addEventListener('load', () => {
  let loadFlag = false
  const openSearch = () => {
    const bodyStyle = document.body.style
    bodyStyle.width = '100%'
    bodyStyle.overflow = 'hidden'
    if (document.getElementById('search-mask')) {
      btf.animateIn(document.getElementById('search-mask'), 'to_show 0.5s')
    }
    if (document.querySelector('#local-search .search-dialog')) {
      btf.animateIn(document.querySelector('#local-search .search-dialog'), 'titleScale 0.5s')
    }
    if (document.querySelector('#local-search-input input')) {
      setTimeout(() => { document.querySelector('#local-search-input input').focus() }, 100)
    }
    if (!loadFlag) {
      try {
        search(GLOBAL_CONFIG.localSearch.path)
        loadFlag = true
      } catch (error) {
        console.error('搜索功能加载失败:', error)
      }
    }
    // shortcut: ESC
    document.addEventListener('keydown', function f (event) {
      if (event.code === 'Escape') {
        closeSearch()
        document.removeEventListener('keydown', f)
      }
    })
  }

  const closeSearch = () => {
    const bodyStyle = document.body.style
    bodyStyle.width = ''
    bodyStyle.overflow = ''
    
    try {
      if (document.querySelector('#local-search .search-dialog')) {
        btf.animateOut(document.querySelector('#local-search .search-dialog'), 'search_close .5s')
      }
      if (document.getElementById('search-mask')) {
        btf.animateOut(document.getElementById('search-mask'), 'to_hide 0.5s')
      }
    } catch (error) {
      console.error('关闭搜索框时出错:', error)
      // 如果动画失败，则直接隐藏元素
      if (document.querySelector('#local-search .search-dialog')) {
        document.querySelector('#local-search .search-dialog').style.display = 'none'
      }
      if (document.getElementById('search-mask')) {
        document.getElementById('search-mask').style.display = 'none'
      }
    }
  }

  // click function
  const searchClickFn = () => {
    const searchButton = document.querySelector('#search-button > .search')
    if (searchButton) {
      searchButton.addEventListener('click', openSearch)
    }
    
    const searchMask = document.getElementById('search-mask')
    if (searchMask) {
      searchMask.addEventListener('click', closeSearch)
    }
    
    const closeButton = document.querySelector('#local-search .search-close-button')
    if (closeButton) {
      closeButton.addEventListener('click', closeSearch)
    }
  }

  try {
    searchClickFn()

    // pjax
    window.addEventListener('pjax:complete', function () {
      try {
        if (getComputedStyle(document.querySelector('#local-search .search-dialog')).display === 'block') {
          closeSearch()
        }
        searchClickFn()
      } catch (error) {
        console.error('pjax切换页面后搜索功能错误:', error)
      }
    })
  } catch (error) {
    console.error('初始化搜索功能时出错:', error)
  }

  async function search (path) {
    let datas = []
    try {
      const typeF = path.split('.')[1]
      const response = await fetch(GLOBAL_CONFIG.root + path)
      if (typeF === 'json') {
        datas = await response.json()
      } else if (typeF === 'xml') {
        const res = await response.text()
        const t = await new window.DOMParser().parseFromString(res, 'text/xml')
        const a = await t
        datas = [...a.querySelectorAll('entry')].map(function (item) {
          return {
            title: item.querySelector('title').textContent,
            content: item.querySelector('content').textContent,
            url: item.querySelector('url').textContent
          }
        })
      }
      if (response.ok) {
        const $loadDataItem = document.getElementById('loading-database')
        if ($loadDataItem && $loadDataItem.nextElementSibling) {
          $loadDataItem.nextElementSibling.style.display = 'block'
          $loadDataItem.remove()
        }
      }
    } catch (error) {
      console.error('获取搜索数据失败:', error)
      return
    }

    const $input = document.querySelector('#local-search-input input')
    const $resultContent = document.getElementById('local-search-results')
    const $loadingStatus = document.getElementById('loading-status')
    
    if (!$input || !$resultContent) {
      console.error('搜索框元素不存在')
      return
    }

    $input.addEventListener('input', function () {
      const keywords = this.value.trim().toLowerCase().split(/[\s]+/)
      if (keywords[0] !== '' && $loadingStatus) $loadingStatus.innerHTML = '<i class="fas fa-spinner fa-pulse"></i>'

      $resultContent.innerHTML = ''
      let str = '<div class="search-result-list">'
      if (this.value.trim().length <= 0) return
      let count = 0
      // perform local searching
      datas.forEach(function (data) {
        let isMatch = true
        if (!data.title || data.title.trim() === '') {
          data.title = ''
        }
        let dataTitle = data.title.trim().toLowerCase()
        const dataContent = data.content ? data.content.trim().replace(/<[^>]+>/g, '').toLowerCase() : ''
        const dataUrl = data.url.startsWith('/') ? data.url : GLOBAL_CONFIG.root + data.url
        let indexTitle = -1
        let indexContent = -1
        let firstOccur = -1
        // only match artiles with not empty titles and contents
        if (dataTitle !== '' || dataContent !== '') {
          keywords.forEach(function (keyword, i) {
            indexTitle = dataTitle.indexOf(keyword)
            indexContent = dataContent.indexOf(keyword)
            if (indexTitle < 0 && indexContent < 0) {
              isMatch = false
            } else {
              if (indexContent < 0) {
                indexContent = 0
              }
              if (i === 0) {
                firstOccur = indexContent
              }
            }
          })
        } else {
          isMatch = false
        }

        // show search results
        if (isMatch) {
          const content = data.content.trim().replace(/<[^>]+>/g, '')
          if (firstOccur >= 0) {
            // cut out 130 characters
            let start = firstOccur - 30
            let end = firstOccur + 100

            if (start < 0) {
              start = 0
            }

            if (start === 0) {
              end = 100
            }

            if (end > content.length) {
              end = content.length
            }

            let matchContent = content.substring(start, end)

            // highlight all keywords
            keywords.forEach(function (keyword) {
              const regS = new RegExp(keyword, 'gi')
              matchContent = matchContent.replace(regS, '<span class="search-keyword">' + keyword + '</span>')
              dataTitle = dataTitle.replace(regS, '<span class="search-keyword">' + keyword + '</span>')
            })

            str += '<div class="local-search__hit-item"><a href="' + dataUrl + '" class="search-result-title">' + dataTitle + '</a>'
            count += 1

            if (dataContent !== '') {
              str += '<p class="search-result">' + matchContent + '...</p>'
            }
          }
          str += '</div>'
        }
      })
      if (count === 0) {
        if (GLOBAL_CONFIG.localSearch.languages && GLOBAL_CONFIG.localSearch.languages.hits_empty) {
          str += '<div id="local-search__hits-empty">' + GLOBAL_CONFIG.localSearch.languages.hits_empty.replace(/\$\{query}/, this.value.trim()) + '</div>'
        } else {
          str += '<div id="local-search__hits-empty">找不到您查询的内容：' + this.value.trim() + '</div>'
        }
      }
      str += '</div>'
      $resultContent.innerHTML = str
      if (keywords[0] !== '' && $loadingStatus) $loadingStatus.innerHTML = ''
      window.pjax && window.pjax.refresh($resultContent)
    })
  }

  // 处理简单搜索功能
  const setupSimpleSearch = () => {
    // 处理常规搜索按钮
    const searchButtons = document.querySelectorAll('#search-button .search, #sidebar-search-button .search');
    searchButtons.forEach(searchButton => {
      // 如果按钮存在且不是展开型搜索框
      if (searchButton && !searchButton.querySelector('#dSearch, #sidebar-dSearch')) {
        searchButton.addEventListener('click', function(e) {
          e.preventDefault();
          try {
            // 跳转到搜索页
            window.location.href = window.location.origin + '/index.php/search/';
          } catch (error) {
            console.error('跳转搜索页失败:', error);
          }
        });
      }
    });
  };

  // 设置简单搜索
  setupSimpleSearch();

  // 以下是伸展搜索框功能
  const setupExpandableSearch = (inputId, formId, buttonSelector) => {
    const searchInput = document.getElementById(inputId);
    const searchButton = document.querySelector(buttonSelector);
    const searchForm = document.getElementById(formId);

    if (searchButton && searchInput && searchForm) {
      // 点击搜索按钮展开搜索框
      searchButton.addEventListener('click', function(e) {
        e.preventDefault();
        if (searchInput.classList.contains('expanded')) {
          // 如果已经展开且有内容，则提交表单
          if (searchInput.value.trim() !== '') {
            searchForm.submit();
          } else {
            // 如果没有内容，则收起搜索框
            searchInput.classList.remove('expanded');
          }
        } else {
          // 展开搜索框
          searchInput.classList.add('expanded');
          searchInput.focus();
        }
      });

      // 搜索框失去焦点时，如果没有内容则收起
      searchInput.addEventListener('blur', function() {
        if (searchInput.value.trim() === '') {
          setTimeout(() => {
            searchInput.classList.remove('expanded');
          }, 200);
        }
      });

      // 按下回车键时提交搜索
      searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && searchInput.value.trim() !== '') {
          searchForm.submit();
        }
      });
    }
  };

  // 设置桌面版搜索框
  setupExpandableSearch('search-input', 'search-form', '.desktop-search .search-button');
  
  // 设置移动版搜索框
  setupExpandableSearch('mobile-search-input', 'mobile-search-form', '.mobile-search .search-button');

  // 添加手机端侧边栏搜索按钮的支持
  const searchButtons = document.querySelectorAll('#search-button .search, #sidebar-search-button .search, #mobile-search-button .search');
  searchButtons.forEach(btn => {
    btn.addEventListener('click', function() {
      openSearch();
    });
  });
}); 