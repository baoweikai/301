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
          <a-input
            size="large"
            type="password"
            autocomplete="false"
            placeholder="确认密码: 和密码保持一致"
            v-decorator="[
              'repeat',
              { rules: [{ required: true, message: '请输入确认密码' }], validateTrigger: 'blur' }
           ]"
          >
            <a-icon slot="prefix" type="lock" :style="{ color: 'rgba(0,0,0,.25)' }" />
          </a-input>
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
          >确定</a-button>
        </a-form-item>
      </a-form>
    </a-card>
  </div>
</template>

<script>
import md5 from 'md5'
import { http, page } from '@/utils'

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
      }
    }
  },
  mounted () {
    // this.load('me/pass')
  },
  methods: {
    handleSubmit (e) {
      e.preventDefault()
      this.state.loginBtn = true

      this.form.validateFields((err, values) => {
        if (!err) {
          const params = { password: md5(values.password), repeat: md5(values.repeat) }

          http.post('me/pass', params)
            .then(res => {
              if (!res.state || res.state === 200) {
                this.Login(res)
                this.loginSuccess(res)
              }
            }).catch(e => {
            }).finally(() => {
              this.state.loginBtn = false
            })
        } else {
          this.state.loginBtn = false
        }
      })
    }
  }
}
</script>

<style lang="less" scoped>

</style>
