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
      // 站长
      {
        path: '/master/',
        redirect: '/master/index',
        component: Page,
        meta: { title: '站长管理', icon: 'shopping' },
        children: [
          {
            path: 'index',
            name: 'MasterIndex',
            component: () => import('@/views/master/MasterIndex'),
            meta: { title: '站长' }
          },
          {
            path: 'siter',
            name: 'SiterIndex',
            component: () => import('@/views/master/SiterIndex'),
            meta: { title: '站点' }
          }
        ]
      },
      // 资源
      {
        path: '/res/',
        redirect: '/res/vedio',
        component: Page,
        meta: { title: '资源管理', icon: 'shopping' },
        children: [
          {
            path: 'video',
            name: 'VedioIndex',
            component: () => import('@/views/res/VideoIndex'),
            meta: { title: '视频', keepAlive: true }
          },
          {
            path: 'route',
            name: 'RouteIndex',
            component: () => import('@/views/res/RouteIndex'),
            meta: { title: '播放线路', keepAlive: true }
          },
          {
            path: 'novel',
            name: 'NovelIndex',
            component: () => import('@/views/res/NovelIndex'),
            meta: { title: '小说' }
          },
          {
            path: 'novel/add',
            name: 'NovelAdd',
            hidden: true,
            component: () => import('@/views/res/NovelAdd'),
            meta: { title: '小说添加' }
          },
          {
            path: 'picture',
            name: 'PictureIndex',
            component: () => import('@/views/res/PictureIndex'),
            meta: { title: '图片' }
          },
          {
            path: 'picture/add',
            name: 'PictureAdd',
            hidden: true,
            component: () => import('@/views/res/PictureAdd'),
            meta: { title: '图片添加' }
          },
          {
            path: 'video-upload',
            name: 'UploadIndex',
            component: () => import('@/views/res/UploadIndex'),
            meta: { title: '视频上传', keepAlive: true }
          },
          {
            path: 'upload-route',
            name: 'UploadRouteIndex',
            component: () => import('@/views/res/UploadRouteIndex'),
            meta: { title: '上传线路', keepAlive: true }
          }
        ]
      },
      /*
      // 广告管理
      {
        path: '/adv/',
        component: Page,
        redirect: '/adv/index',
        meta: { title: '广告管理', icon: 'shopping' },
        children: [
          {
            path: 'index',
            name: 'AdvIndex',
            component: () => import('@/views/adv/AdvIndex'),
            meta: { title: '广告列表', keepAlive: true }
          },
          {
            path: 'stat',
            name: 'AdvStat',
            component: () => import('@/views/adv/AdvStat'),
            meta: { title: '广告统计', keepAlive: true }
          }
        ]
      },
      */
      // 分类标签
      {
        path: '/catetage/',
        component: Page,
        redirect: '/catetage/cate',
        meta: { title: '分类标签', icon: 'shopping' },
        children: [
          {
            path: 'cate',
            name: 'CateIndex',
            component: () => import('@/views/catetag/CateIndex'),
            meta: { title: '分类', keepAlive: true }
          },
          {
            path: 'tag',
            name: 'TagIndex',
            component: () => import('@/views/catetag/TagIndex'),
            meta: { title: '标签', keepAlive: true }
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
            path: 'score',
            name: 'ScoreIndex',
            component: () => import('@/views/user/ScoreIndex'),
            meta: { title: '会员统计', keepAlive: true }
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
