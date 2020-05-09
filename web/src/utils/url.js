const url = {
  base (controller, action, id) {
    const path = '/' + controller + '/' + (action || 'index') + (id ? '/' + id : '')
    return path
  },
  index (controller, parameter = {}) {
    return this.base(controller, 'index')
  },
  view (controller, id) {
    return this.base(controller, 'view', id)
  },
  add (controller, parameter = {}) {
    return this.base(controller, 'add')
  },
  edit (controller, id) {
    return this.base(controller, 'edit', id)
  },
  save (controller) {
    return this.base(controller, 'save')
  },
  update (controller, id) {
    return this.base(controller, 'update', id)
  },
  del (controller, id) {
    return this.base(controller, 'del', id)
  },
  patch (controller, id) {
    return this.base(controller, 'patch', id)
  },
  upload (controller) {
    return this.base(controller, 'upload')
  }
}
export default url
