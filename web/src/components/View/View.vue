<template>
  <a-info :info="info">
    <Item
      v-for="(column, index) in pick(cols, ['prod_name', 'picture', 'price', 'state', 'update_at'])"
      :key="index"
      v-bind="{ column: column, index: index, data: $attrs.data, info: info }"
    />
    <a-info-item
      :wrapperCol="{ span: 24 }"
      style="text-align: center"
    >
      <a-button @click="submit()" type="primary">提交</a-button>
      <a-button style="margin-left: 8px">保存</a-button>
    </a-info-item>
  </a-info>
</template>

<script>
import Item from '../Item/Item'
import { view } from '@/utils'

export default {
  mixins: [view],
  components: {
    Item
  },
  props: ['controller', 'items', 'cols'],
  data () {
    return {
    }
  },
  created () {
    this.info(this.$route.params.id)
  },
  methods: {
  }
}
</script>
