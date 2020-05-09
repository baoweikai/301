<template>
  <a-breadcrumb :routes="breadList">
    <template slot="itemRender" slot-scope="{route, params, routes, paths}">
      <span v-if="routes.indexOf(route) === routes.length - 1">
        {{route.breadcrumbName}}
      </span>
      <router-link v-else :to="`/${paths.join('/')}`">
        <a-icon :type="route.icon" /> {{route.breadcrumbName}}
      </router-link>
    </template>
  </a-breadcrumb>
</template>

<script>
export default {
  data () {
    return {
      name: '',
      breadList: []
    }
  },
  created () {
    this.getBreadcrumb()
  },
  methods: {
    getBreadcrumb () {
      this.breadList = []

      this.name = this.$route.name
      this.$route.matched.forEach(item => {
        this.breadList.push({
          path: item.path,
          icon: item.meta.icon,
          breadcrumbName: item.meta.title
        })
      })
    }
  },
  watch: {
    $route () {
      this.getBreadcrumb()
    }
  }
}
</script>

<style scoped>
</style>
