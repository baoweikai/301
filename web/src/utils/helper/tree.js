export default {
  list: [],
  load (pid) {
    return Object.values(this.list).filter(row => row.pid === pid)
  },
  pids (id, ids = []) {
    ids.unshift(id)
    this.list[id] && this.pids(this.list[id].pid, ids)
    return ids
  }
}