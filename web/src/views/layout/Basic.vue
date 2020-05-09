<template>
  <div class="fdc" style="height: 100vh;">
    <router-view class="f1" />
    <div class="fdc fac tac py2">
      <div class="f tac">
        <router-link to="_self">帮助</router-link>
        <router-link to="_self">隐私</router-link>
        <router-link to="_self">条款</router-link>
      </div>
      <div class="py1">
        Copyright &copy; 2020 猫咪技术组出品
      </div>
    </div>
  </div>
</template>

<script>
import { mixinDevice } from '@/utils/mixin/app'

export default {
  mixins: [mixinDevice],
  data () {
    return {}
  },
  mounted () {
    document.body.classList.add('userLayout')
  },
  beforeDestroy () {
    document.body.classList.remove('userLayout')
  }
}
</script>

<style lang="less" scoped>
.logo{ height: 48px; }
</style>
