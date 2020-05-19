<template>
  <div class="fjc">
    <a-card class="bg-white">
      <a-form
        class="w30"
        ref="formLogin"
        :form="form"
        @submit="handleSubmit"
      >
        <a-form-item>
          <a-input
            size="large"
            type="text"
            placeholder="账户: 字母数字组合"
            v-decorator="[
              'username',
              { rules: [{ required: true, message: '请输入帐户名' }], validateTrigger: 'change' }
           ]"
          >
            <a-icon slot="prefix" type="user" :style="{ color: 'rgba(0,0,0,.25)' }"/>
          </a-input>
        </a-form-item>

        <a-form-item>
          <a-input
            size="large"
            type="password"
            autocomplete="false"
            placeholder="密码: 不少于6位字母和数字"
            v-decorator="[
              'password',
              { rules: [{ required: true, message: '请输入密码' }], validateTrigger: 'blur' }
           ]"
          >
            <a-icon slot="prefix" type="lock" :style="{ color: 'rgba(0,0,0,.25)' }" />
          </a-input>
        </a-form-item>
        <a-form-item>
          <a-row :gutter="4">
            <a-col :span="12">
              <a-input
                size="large"
                type="text"
                autocomplete="false"
                placeholder="验证码: 5个字符"
                v-decorator="[
                  'captcha',
                  { rules: [{ required: true, message: '请输入验证码' }], validateTrigger: 'blur' }
              ]" />
            </a-col>
            <a-col :span="12">
              <img :src="'/api/captcha.html?' + t" @click="t = (new Date()).valueOf()" />
            </a-col>
          </a-row>
        </a-form-item>
        <a-form-item style="pt5 fjc">
          <a-button
            size="large"
            type="primary"
            htmlType="submit"
            block
            class="login-button"
            :loading="state.loginBtn"
            :disabled="state.loginBtn"
          >登录</a-button>
        </a-form-item>
      </a-form>
    </a-card>
  </div>
</template>

<script>
import md5 from 'md5'
import { mapActions } from 'vuex'
import { http, util, page } from '@/utils'

export default {
  mixins: [page],
  components: {
  },
  data () {
    return {
      loginBtn: false,
      form: this.$form.createForm(this),
      state: {
        time: 60,
        loginBtn: false
      },
      t: 0
    }
  },
  mounted () {
  },
  methods: {
    ...mapActions(['Login', 'Logout']),
    handleSubmit (e) {
      e.preventDefault()
      this.state.loginBtn = true

      this.form.validateFields((err, values) => {
        if (!err) {
          const loginParams = { ...values,
            grant_type: 'password',
            client_id: '2',
            client_secret: 'dLUF49huM6RloQ76wQ6wVf5WaMkG5sPxPUOZQUYQ',
            scope: '*'
          }
          loginParams.password = md5(values.password)

          http.post('identity/login', loginParams)
            .then(res => {
              if (res.state === 200) {
                console.log(res)
                // this.Login(res.result)
                this.$router.push({ name: 'Dashboard' }).catch(e => {})
                // 延迟 1 秒显示欢迎信息
                setTimeout(() => {
                  util.notify(`${util.timeFix()}，欢迎回来`)
                }, 1000)
              } else if (res.state < 300) {
                this.t = (new Date()).valueOf()
              }
            }).catch(() => {
              // console.log('catch:', e)
            }).finally(() => {
              this.state.loginBtn = false
            })
        } else {
          this.state.loginBtn = false
        }
      })
    },
    loginSuccess (res) {
    }
  }
}
</script>

<style lang="less" scoped>

</style>
