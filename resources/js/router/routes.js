import { createRouter, createWebHistory } from "vue-router"; // <--- 1. BU IMPORT YETISHMAYOTGAN EDI

// 2. Rollarni massivga olamiz (export default qilib yubormaymiz)
const routes = [
  {
    path: "/",
    name: "home",
    component: () => import("../pages/HomePage.vue"),
  },
  {
    path: "/login",
    name: "Login",
    component: () => import("../pages/Login.vue"), // Importni funksiya ichiga oldim (Lazy loading)
    meta: { guest: true }, // Mehmonlar uchun
  },

  // 3. ADMIN PANEL
  {
    path: "/admin",
    component: () => import("../layouts/AdminLayout.vue"),
    meta: { requiresAuth: true },
    children: [
      {
        path: "", // /admin deb kirganda Dashboard chiqadi
        name: "admin-dashboard",
        component: () => import("../pages/admin/Dashboard.vue"),
      },
      // Universal CRUD (agar kerak bo'lsa)
      // {
      //   path: ":resource",
      //   name: "universal-crud",
      //   component: () => import("../pages/UniversalPage.vue"),
      // },
      {
        path: "users",
        name: "admin-users",
        component: () => import("../pages/admin/UsersPage.vue"),
      },
      {
        path: "roles",
        name: "admin-roles",
        component: () => import("../pages/admin/RolesPage.vue"),
      },
      {
        path: "pages",
        name: "admin-pages",
        component: () => import("../pages/admin/PagesPage.vue"),
      },
      {
        path: "groups",
        name: "admin-groups",
        component: () => import("../pages/admin/GroupsPage.vue"),
      },
      {
        path: "transport-groups",
        name: "admin-trans",
        component: () => import("../pages/admin/TransportGroups.vue"),
      },
      {
        path: "geozone-groups",
        name: "admin-geozone-groups",
        component: () => import("../pages/admin/GeozoneGroups.vue"),
      },
      {
        path: "machines",
        name: "admin-machines",
        component: () => import("../pages/admin/MachinesPage.vue"),
      },
      {
        path: "logs",
        name: "SystemLogs",
        component: () => import("../pages/admin/SystemLogs.vue"),
      },
      {
        path: "wialon",
        name: "admin-wialon",
        component: () => import("../pages/admin/WialonPage.vue"),
      },
    ],
  },
  {
    path: "/:pathMatch(.*)*",
    redirect: "/",
    name: "pathMatch",
    meta: {
      title: "all",
    },
  },
];

// 4. Routerni yaratamiz
const router = createRouter({
  history: createWebHistory(),
  routes, // Yuqoridagi massivni ulaymiz
});

// 5. Himoya (Guard)
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem("token");

  // Agar sahifa himoyalangan bo'lsa va token bo'lmasa -> Loginga
  if (to.meta.requiresAuth && !token) {
    return next("/login");
  }

  // Agar user Login sahifasiga kirmoqchi bo'lsa, lekin allaqachon kirgan bo'lsa -> Dashboardga
  if (to.meta.guest && token) {
    return next("/admin/dashboard");
  }

  next();
});

// 6. Routerni eksport qilamiz (massivni emas!)
export default router;
