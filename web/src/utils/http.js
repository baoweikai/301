import Vue from 'vue'
import { ACCESS_TOKEN } from '@/store/mutation-types'
import { util, url } from './index'
import axios from 'axios'

const codes = {
  200: '', // 正确的请求返回正确的结果，如果不想细分正确的请求结果都可以直接返回200。',
  201: '', // 表示资源被正确的创建。比如说，我们 POST 用户名、密码正确创建了一个用户就可以返回 201。
  202: '', // 请求是正确的，但是结果正在处理中，这时候客户端可以通过轮询等机制继续请求。',
  203: '', // 请求的代理服务器修改了源服务器返回的 200 中的内容，我们通过代理服务器向服务器 A 请求用户信息，服务器 A 正常响应，但代理服务器命中了缓存并返回了自己的缓存内容，这时候它返回 203 告诉我们这部分信息不一定是最新的，我们可以自行判断并处理。',
  300: '', // 请求成功，但结果有多种选择。',
  301: '', // 请求成功，但是资源被永久转移。比如说，我们下载的东西不在这个地址需要去到新的地址。',
  303: '', // 使用 GET 来访问新的地址来获取资源。',
  304: '', // 请求的资源并没有被修改过。',
  308: '', // 使用原有的地址请求方式来通过新地址获取资源。',
  400: '请求出现错误', // 请求出现错误，比如请求头不对等。',
  401: '无效的身份信息, 请先登录', // 没有提供认证信息。请求的时候没有带上 Token 等。',
  402: '请求出现错误', // 为以后需要所保留的状态码。',
  403: '无权访问该页面', // 请求的资源不允许访问。就是说没有权限。',
  404: '请求的内容不存在', // 请求的内容不存在。',
  406: '请求的资源并不符合要求', // 请求的资源并不符合要求。',
  408: '客户端请求超时', // 客户端请求超时。',
  413: '请求体过大', // 请求体过大。',
  415: '类型不正确', // 类型不正确。',
  416: '请求的区间无效', // 请求的区间无效。',',
  500: '服务器错误', // 服务器错误。',
  501: '请求还没有被实现', // 请求还没有被实现。',
  502: '网关错误', // 网关错误。',
  503: '服务暂时不可用', // 服务暂时不可用。服务器正好在更新代码重启。',
  505: '请求的 HTTP 版本不支持' // 请求的 HTTP 版本不支持。',
}

const http = {
  request () {
    // axios.defaults.withCredentials = true
    // 创建 axios 实例
    const service = axios.create({
      withCredentials: true,
      baseURL: '/api', // api base_url
      timeout: 6000 // 请求超时时间
    })
    // request interceptor
    service.interceptors.request.use(config => {
      const token = Vue.ls.get(ACCESS_TOKEN)
      token && (config.headers[ACCESS_TOKEN] = 'bearer ' + token) // 让每个请求携带自定义 token 请根据实际情况自行修改
      localStorage.csrf_token && (config.headers['X-CSRF-TOKEN'] = localStorage.csrf_token)
      return config
    }, ({ response: { data } }) => {
      util.notify(data.message, 'error')
    })
    // response interceptor
    service.interceptors.response.use(({ data, status }) => {
      if (status === 200 || data.state === 200) {
        util.message(data.message)
        data.result && data.result.csrf_token && (localStorage.csrf_token = data.result.csrf_token)
        return data.result || data
      } else if (status < 300) {
        util.message(data.message, 'info')
        return data
      } else {
        util.message(codes[status], 'error')
      }
      return false
    }, ({ response: { data, status } }) => {
      console.log(status)
      if (status === 401) {
        util.navto('/login')
      }
      return false
    })

    return service
  },
  async get (path, params = {}) {
    const response = await this.request().get(path, { params: params })

    if (response.state === 401) {
      util.navto('/login')
    }
    return response
  },
  // 列表
  async index (controller, params = {}) {
    const ret = this.get(url.index(controller), params)
    return ret
  },
  // 模块信息页
  async view (controller, id, params = {}) {
    const ret = await this.get(url.view(controller, id), params)
    return ret
  },
  // 模块添加
  async add (controller, params = {}) {
    const ret = await this.get(url.add(controller), params)
    return ret
  },
  // 模块编辑
  async edit (controller, id, params = {}) {
    const ret = await this.get(url.edit(controller, id), params)
    return ret
  },
  // 模块保存
  async save (controller, params = {}) {
    const ret = await this.post(url.save(controller), params)
    return ret
  },
  // 模块更新
  async update (controller, id, params = {}) {
    const ret = await this.request().put(url.update(controller, id), params)
    return ret
  },
  async post (path, params = {}) {
    const ret = await this.request().post(path, params)
    return ret
  },
  async del (controller, id, params = {}) {
    const ret = await this.request().delete(url.del(controller, id), params)
    return ret
  },
  async image (path, params = {}) {
    const ret = await this.request().get(path, params)
    return ret
  },
  async upload (action, data) {
    const formData = new FormData()
    for (const i in data) {
      formData.append(i, data[i])
    }
    const ret = await this.request().post(action, formData)
    return ret
  }
}
export default http
