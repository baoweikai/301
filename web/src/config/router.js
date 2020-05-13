// eslint-disable-next-line
import { Common, Basic, Page } from '@/views/layout'

export const asyncRouterMap = [
  {
    path: '/',
    name: 'index',
    component: Common,
    meta: { title: '首页', icon: 'home' },
    redirect: '/dashboard/workplace',
    children: [
      {
        path: '/dashboard/workplace',
        name: 'Dashboard',
        component: () => import('@/views/dashboard/Workplace'),
        meta: { title: '仪表盘', keepAlive: true, icon: 'home' }
      },
      // 资源
      {
        path: '/res/',
        redirect: '/res/domain',
        component: Page,
        meta: { title: '资源管理', icon: 'shopping' },
        children: [
          {
            path: 'domain',
            name: 'DomainIndex',
            component: () => import('@/views/res/DomainIndex'),
            meta: { title: '域名', keepAlive: true }
          },
          {
            path: 'group',
            name: 'GroupIndex',
            component: () => import('@/views/res/GroupIndex'),
            meta: { title: '引量分组' }
          },
          {
            path: 'cited-domain',
            name: 'CitedDomainIndex',
            component: () => import('@/views/res/CitedDomainIndex'),
            meta: { title: '引量地址' }
          },
          {
            path: 'cname',
            name: 'CnameIndex',
            component: () => import('@/views/res/CnameIndex'),
            meta: { title: '别名' }
          },
          {
            path: 'cate',
            name: 'CateIndex',
            component: () => import('@/views/res/CateIndex'),
            meta: { title: '分类', keepAlive: true }
          }
        ]
      },
      // 统计
      {
        path: '/stat/',
        redirect: '/stat/date',
        component: Page,
        meta: { title: '统计数据', icon: 'shopping' },
        children: [
          {
            path: 'date',
            name: 'DateIndex',
            component: () => import('@/views/stat/DateIndex'),
            meta: { title: '日报' }
          }
        ]
      },
      // 权限
      {
        path: '/auth/',
        component: Page,
        redirect: 'admin',
        meta: { title: '权限管理', icon: 'crown' },
        children: [
          {
            path: 'role',
            name: 'RoleIndex',
            component: () => import('@/views/auth/RoleIndex'),
            meta: { title: '角色', keepAlive: true }
          },
          {
            path: 'admin',
            name: 'AdminIndex',
            component: () => import('@/views/auth/AdminIndex'),
            meta: { title: '管理员', keepAlive: true }
          }
        ]
      },
      // 用户
      {
        path: '/user/',
        component: Page,
        redirect: '/user/index',
        meta: { title: '用户管理', icon: 'user' },
        children: [
          {
            path: 'index',
            name: 'UserIndex',
            component: () => import('@/views/user/UserIndex'),
            meta: { title: '会员', keepAlive: true }
          },
          {
            path: 'expense',
            name: 'ExpenseIndex',
            component: () => import('@/views/user/ExpenseIndex'),
            meta: { title: '消费', keepAlive: true }
          },
          {
            path: 'recharge',
            name: 'RechargeIndex',
            component: () => import('@/views/user/RechargeIndex'),
            meta: { title: '充值', keepAlive: true }
          }
        ]
      },
      // 系统
      {
        path: '/sys/',
        component: Page,
        redirect: '/sys/article',
        meta: { title: '系统设置', icon: 'setting' },
        children: [
          {
            path: 'article',
            name: 'ArticleIndex',
            component: () => import('@/views/sys/ArticleIndex'),
            meta: { title: '公告咨询', keepAlive: true }
          },
          {
            path: 'help',
            name: 'HelpIndex',
            component: () => import('@/views/sys/HelpIndex'),
            meta: { title: '帮助', keepAlive: true }
          },
          {
            path: '/profile',
            name: 'Profile',
            component: () => import('@/views/sys/Profile'),
            meta: { title: '个人设置', keepAlive: true }
          },
          {
            path: '/password',
            name: 'Password',
            component: () => import('@/views/sys/Password'),
            meta: { title: '修改密码', keepAlive: true }
          }
        ]
      }
    ]
  }
]
/**
 * 基础路由
 * @type { *[] }
 */
export const constRouterMap = [
  {
    path: '/public',
    component: Basic,
    redirect: '/login',
    hidden: true,
    children: [
      {
        path: '/login',
        name: 'Login',
        component: () => import(/* webpackChunkName: "user" */ '@/views/public/Login')
      },
      {
        path: '/signup',
        name: 'Signup',
        component: () => import(/* webpackChunkName: "user" */ '@/views/public/Signup')
      },
      {
        path: '/forget',
        name: 'Forget',
        component: () => import(/* webpackChunkName: "user" */ '@/views/public/Forget')
      },
      {
        path: '/result',
        name: 'Result',
        component: () => import(/* webpackChunkName: "user" */ '@/views/public/Result')
      }
    ]
  },
  {
    path: '/404',
    component: () => import(/* webpackChunkName: "fail" */ '@/views/exception/404')
  }
]
