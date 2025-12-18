<script setup>
import {ref} from "vue";
import {useRouter, useRoute} from 'vue-router';
const route = useRoute();
const router = useRouter();

// import {ArrowRightBold} from '@element-plus/icons-vue';

const isMenuOpen = ref(false);

const routes = ref([
  {
    name: 'Settings',
    icon: "Gift",
    url: '/settings',
    child: [
      {
        name: 'General',
        url: '/settings'
      }
    ]
  }
]);

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value;
}

const isRouteActive = (tabRoute) => {
  const currentPath = route.path;

  // First check if any child route matches exactly
  if (tabRoute.child && tabRoute.child.length) {
    const childMatches = tabRoute.child.some(child => currentPath === child.url || currentPath.includes(child.url));
    if (childMatches) {
      return true;
    }
  }

  // Then check if current path exactly matches the parent route
  if (currentPath === tabRoute.url || currentPath.includes(tabRoute.url)) {
    return true;
  }
  return false;
};


const isChildActive = (child, index, routeGroup) => {
  const currentPath = route.path;

  // Exact match for child
  if (currentPath === child.url) {
    return true;
  }

  // Special case: when on parent route, mark first child active
  if (currentPath === routeGroup.url && index === 0) {
    return true;
  }

  return false;
};

</script>

<template>
  <div class="fluentreservation-settings-wrapper">
    <div class="fluentreservation-settings-sidebar-panel">
      <ul>
        <li v-for="(route, i) in routes" :key="i" :class="{'fct-tab-item-active': isRouteActive(route)}">
          <router-link :to="route.url">
            {{ route.name }}
<!--            <el-icon v-if="route.child"><ArrowRightBold /></el-icon>-->
          </router-link>
          <!-- Child Components -->
          <ul v-if="isRouteActive(route) && route.child" class="fct-settings-menu-child-wrap">
            <li v-for="(child, i) in route.child" :key="i" @click="toggleMenu"
                      :class="{ 'fct-tab-item-active': isChildActive(child, i, route) }"
            >
              <router-link :to="child.url">
                {{ child.name }}
              </router-link>
            </li>
          </ul>
        </li>
      </ul>
    </div>
    <div class="fluentreservation-settings-main-panel">
      <router-view/>
    </div>
  </div>
</template>

<style scoped>

</style>
