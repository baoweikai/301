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
            placeholder="账户: admin"
            v-decorator="[
              'username',
              {rules: [{ required: true, message: '请输入帐户名' }], validateTrigger: 'change'}
           ]"
          >
            <a-icon slot="prefix" type="user" :style="{ color: 'rgba(0,0,0,.25)' }"/>
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
    this.load('me/info')
  },
  methods: {
    handleSubmit (e) {
      e.preventDefault()

      this.form.validateFields((err, values) => {
        if (!err) {
          /*
          const loginParams = { ...values,
            grant_type: 'password',
            client_id: '2',
            client_secret: 'dLUF49huM6RloQ76wQ6wVf5WaMkG5sPxPUOZQUYQ',
            scope: '*'
          }
          */
          const params = { password: md5(values.password) }

          this.state.loginBtn = true
          http.post('me/pass', params)
            .then(res => {
              if (res.state === 200) {
                this.$message.success('修改成功')
              }
            }).catch(e => {}).finally(() => {
              this.state.loginBtn = false
            })
        }
      })
    }
  }
}
</script>

<style lang="less" scoped>

</style>
