<template functional>
  <a-sub-menu :key="props.menu.key">
    <span v-if="props.menu.children" slot="title">
      <a-icon :type="props.menu.icon" /><span>
      {{ props.menu.title }}</span>
    </span>
    <span v-else slot="title">
      <router-link :to="{ name: props.menu.name }">
        <span>{{ props.menu.title }}</span>
      </router-link>
    </span>
    <template v-for="child in props.menu.children">
      <a-menu-item v-if="!child.children" :key="child.key">
        <router-link :to="{ name: child.name }">
          <span>{{child.title}}</span>
        </router-link>
      </a-menu-item>
      <sub-menu v-else :key="child.key" :menus="child" />
    </template>
  </a-sub-menu>
</template>
<script>
export default {
  props: {
    menu: {
      type: Object,
      default: () => ({})
    }
  }
}
</script>
