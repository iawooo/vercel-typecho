window.addEventListener('load', () => {
  let loadFlag = false
  const openSearch = () => {
    const bodyStyle = document.body.style
    bodyStyle.width = '100%'
    bodyStyle.overflow = 'hidden'
    
    const searchMask = document.getElementById('search-mask')
    const searchDialog = document.querySelector('#local-search .search-dialog')
    
    if (searchMask) {
      btf.animateIn(searchMask, 'to_show 0.5s')
    }
    
    if (searchDialog) {
      btf.animateIn(searchDialog, 'titleScale 0.5s')
    }
    
    const searchInput = document.querySelector('#local-search-input input')
    if (searchInput) {
      setTimeout(() => { searchInput.focus() }, 100)
    }
    
    if (!loadFlag) {
      search(GLOBAL_CONFIG.localSearch.path)
      loadFlag = true
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
    
    const searchDialog = document.querySelector('#local-search .search-dialog')
    const searchMask = document.getElementById('search-mask')
    
    if (searchDialog) {
      btf.animateOut(searchDialog, 'search_close .5s')
    }
    
    if (searchMask) {
      btf.animateOut(searchMask, 'to_hide 0.5s')
    }
  }

  // click function
  const searchClickFn = () => {
    const searchButton = document.querySelector('#search-button > .search');
    if (searchButton) {
      searchButton.addEventListener('click', openSearch);
    } else {
      // 尝试直接查找搜索按钮
      const altSearchButton = document.querySelector('#search-button');
      if (altSearchButton) {
        altSearchButton.addEventListener('click', openSearch);
      }
    }
    
    const searchMask = document.getElementById('search-mask');
    if (searchMask) {
      searchMask.addEventListener('click', closeSearch);
    }
    
    const closeButton = document.querySelector('#local-search .search-close-button');
    if (closeButton) {
      closeButton.addEventListener('click', closeSearch);
    }
  }

  searchClickFn()

  // pjax
  window.addEventListener('pjax:complete', function () {
    getComputedStyle(document.querySelector('#local-search .search-dialog')).display === 'block' && closeSearch()
    searchClickFn()
  })

  async function search (path) {
    let datas = []
    // 如果path未定义，使用默认路径
    if (!path || path === 'undefined') {
      path = 'search.php'
    }
    
    try {
      const typeF = path.split('.')[1]
      const response = await fetch(GLOBAL_CONFIG.root + path)
      
      if (!response.ok) {
        console.error('搜索索引加载失败: ', response.status, response.statusText)
        return
      }
      
      if (typeF === 'json') {
        datas = await response.json()
      } else if (typeF === 'xml') {
        const res = await response.text()
        try {
          const t = new window.DOMParser().parseFromString(res, 'text/xml')
          datas = [...t.querySelectorAll('entry')].map(function (item) {
            return {
              title: item.querySelector('title') ? item.querySelector('title').textContent : '',
              content: item.querySelector('content') ? item.querySelector('content').textContent : '',
              url: item.querySelector('url') ? item.querySelector('url').textContent : ''
            }
          })
        } catch (e) {
          console.error('XML解析错误: ', e)
        }
      }
      
      const $loadDataItem = document.getElementById('loading-database')
      if ($loadDataItem) {
        const nextElem = $loadDataItem.nextElementSibling
        if (nextElem) nextElem.style.display = 'block'
        $loadDataItem.remove()
      }
    } catch (error) {
      console.error('搜索加载错误: ', error)
    }

    const $input = document.querySelector('#local-search-input input')
    const $resultContent = document.getElementById('local-search-results')
    const $loadingStatus = document.getElementById('loading-status')
    
    if (!$input || !$resultContent) {
      console.error('搜索输入框或结果容器不存在')
      return
    }
    
    $input.addEventListener('input', function () {
      const keywords = this.value.trim().toLowerCase().split(/[\s]+/)
      if ($loadingStatus && keywords[0] !== '') {
        $loadingStatus.innerHTML = '<i class="fas fa-spinner fa-pulse"></i>'
      }

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
        const dataUrl = data.url && data.url.startsWith('/') ? data.url : (GLOBAL_CONFIG.root + (data.url || ''))
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
          try {
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
          } catch (e) {
            console.error('结果处理错误: ', e)
          }
        }
      })
      
      if (count === 0 && GLOBAL_CONFIG.localSearch && GLOBAL_CONFIG.localSearch.languages) {
        const emptyResultText = GLOBAL_CONFIG.localSearch.languages.hits_empty || '找不到结果: ${query}'
        str += '<div id="local-search__hits-empty">' + emptyResultText.replace(/\$\{query}/, this.value.trim()) + '</div>'
      }
      str += '</div>'
      $resultContent.innerHTML = str
      if ($loadingStatus && keywords[0] !== '') {
        $loadingStatus.innerHTML = ''
      }
      window.pjax && window.pjax.refresh($resultContent)
    })
  }
})
