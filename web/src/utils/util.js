import { message, notification } from 'ant-design-vue'
import pick from 'lodash.pick'
import router from '@/utils/perm' // permission control

const tipTypes = {
  success: '成功',
  error: '错误',
  info: '消息'
}

export default {
  pick,
  message (text, type = 'success') {
    text && (text !== '') && message[type](text)
  },
  notify (text, type = 'success') {
    text && (text !== '') && notification[type]({
      message: tipTypes[type],
      description: text || '请求出现错误，请稍后再试',
      duration: 4
    })
  },
  toArray (json) {
    try {
      return JSON.parse(json)
    } catch (e) {
      console.log('err', e.message)
    }
    return []
  },
  timeFix () {
    const time = new Date()
    const hour = time.getHours()
    return hour < 9 ? '早上好' : hour <= 11 ? '上午好' : hour <= 13 ? '中午好' : hour < 20 ? '下午好' : '晚上好'
  },

  welcome () {
    const arr = ['休息一会儿吧', '准备吃什么呢?', '要不要打一把 DOTA', '我猜你可能累了']
    const index = Math.floor(Math.random() * arr.length)
    return arr[index]
  },
  setTitle (title) {
    // export const setDocumentTitle = function (title) {
    document.title = title
    const ua = navigator.userAgent
    // eslint-disable-next-line
    const regex = /\bMicroMessenger\/([\d\.]+)/
    if (regex.test(ua) && /ip(hone|od|ad)/i.test(ua)) {
      const i = document.createElement('iframe')
      i.src = '/favicon.ico'
      i.style.display = 'none'
      i.onload = () => {
        setTimeout(() => {
          i.remove()
        }, 9)
      }
      document.body.appendChild(i)
    }
  },
  /**
   * 触发 window.resize
   */
  triggerWindowResizeEvent () {
    const event = document.createEvent('HTMLEvents')
    event.initEvent('resize', true, true)
    event.eventType = 'message'
    window.dispatchEvent(event)
  },

  handleScrollHeader (callback) {
    let timer = 0

    let beforeScrollTop = window.pageYOffset
    callback = callback || (() => {})
    window.addEventListener(
      'scroll',
      event => {
        clearTimeout(timer)
        timer = setTimeout(() => {
          let direction = 'up'
          const afterScrollTop = window.pageYOffset
          const delta = afterScrollTop - beforeScrollTop
          if (delta === 0) {
            return false
          }
          direction = delta > 0 ? 'down' : 'up'
          callback(direction)
          beforeScrollTop = afterScrollTop
        }, 50)
      },
      false
    )
  },
  /**
   * Remove loading animate
   * @param id parent element id or class
   * @param timeout
   */
  removeLoadingAnimate (id = '', timeout = 1500) {
    if (id === '') {
      return
    }
    setTimeout(() => {
      document.body.removeChild(document.getElementById(id))
    }, timeout)
  },
  /**
   * components util
   */

  /**
   * 清理空值，对象
   * @param children
   * @returns {*[]}
   */
  filterEmpty (children = []) {
    return children.filter(c => c.tag || (c.text && c.text.trim() !== ''))
  },

  /**
   * 获取字符串长度，英文字符 长度1，中文字符长度2
   * @param {*} str
   */
  getStrFullLength (str = '') {
    str.split('').reduce((pre, cur) => {
      const charCode = cur.charCodeAt(0)
      if (charCode >= 0 && charCode <= 128) {
        return pre + 1
      }
      return pre + 2
    }, 0)
  },

  /**
   * 截取字符串，根据 maxLength 截取后返回
   * @param {*} str
   * @param {*} maxLength
   */
  cutStrByFullLength (str = '', maxLength) {
    let showLength = 0
    return str.split('').reduce((pre, cur) => {
      const charCode = cur.charCodeAt(0)
      if (charCode >= 0 && charCode <= 128) {
        showLength += 1
      } else {
        showLength += 2
      }
      if (showLength <= maxLength) {
        return pre + cur
      }
      return pre
    }, '')
  },
  navto (path) {
    router.push({ path: path }).catch(err => {
      console.log(err)
    })
  }
}
