import moment from 'moment'
import api from '@/config/api'

export default {
  // 时间转换
  date (text, format) {
    return moment(text).format(format)
  },
  // 图片
  image (text, isReco = false) {
    if (text && (text.startsWith('http://') || text.startsWith('https://'))) {
      return text
    }
    return isReco ? text.replace(api.Image, '') : (text && text.startsWith(api.Image) ? text : api.Image + text)
  },
  // 状态类型
  state (text) {
    const stateMap = ['default', 'processing', 'success', 'error']
    return stateMap[text]
  },
  // radio, chekbox, select等的值
  label (text, options) {
    if (options && options[text]) {
      return options[text]
    } else {
      return ''
    }
  },
  empty (text, $empty = '空') {
    return text || $empty
  },
  // 数组转换为option数组参数, radio, chekbox, select等会用到
  options (text, type = -1) {
    let data = []
    if (typeof (text) === 'string' && text !== '') {
      data = JSON.parse(text)
    } else if (typeof (text) === 'object') {
      data = text
    }
    const options = type >= 0 ? [{ label: ['全部', '请选择'][type], value: '' }] : []
    for (const i in data) {
      if (typeof (data[i]) === 'string') {
        options.push({ label: data[i], value: parseFloat(i) })
      } else {
        options.push(data[i])
      }
    }
    return options
  },
  // 列表过滤参数参数, radio, chekbox, select等会用到
  filters (text, type = -1) {
    let data = []
    if (typeof (text) === 'string' && text !== '') {
      data = JSON.parse(text)
    } else if (typeof (text) === 'object') {
      data = text
    }
    const filters = type >= 0 ? [{ text: ['全部', '请选择'][type], value: '' }] : []
    for (const i in data) {
      if (typeof (data[i]) === 'string') {
        filters.push({ text: data[i], value: parseFloat(i) })
      } else {
        filters.push(data[i])
      }
    }
    return filters
  }
}
