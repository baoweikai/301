import Vue from 'vue'
import VueStorage from 'vue-ls'
import config from '@/config/default'

Vue.use(VueStorage, config.storageOptions)
/**
 * Set storage
 *
 * @param name
 * @param content
 * @param maxAge
 */
export default {
  set (name, content, expire = 0) {
    if (!name) {
      return
    }

    Vue.ls.set(name, content, expire * 1000)
  },
  /**
   * Get storage
   *
   * @param name
   * @returns {*}
   */
  get (name, def = []) {
    if (!name) {
      return
    }

    return Vue.ls.get(name, def)
  },
  /**
   * Clear storage
   *
   * @param name
   */
  remove (name) {
    Vue.ls.remove(name)
  },
  /**
   * Clear all storage
   */
  clear () {
    Vue.ls.clear()
  }
}
