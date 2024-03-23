<template>
  <div class="card settings-card no-print">
    <div class="card-header">{{ $t("common.settings") }}</div>
    <div class="card-body">
      <ul class="nav flex-column nav-pills m-1">
        <li class="nav-item" v-if="developer">
          <router-link
            :to="{ name: 'permissions.index' }"
            class="nav-link thumb"
          >
            <i class="fas fa-braille" />
            {{ $t("setup.index.menu.item_eight.title") }}
          </router-link>
        </li>

        <li class="nav-item">
          <router-link :to="{ name: 'roles.index' }" class="nav-link thumb">
            <i class="fas fa-user-lock" />
            {{ $t("setup.index.menu.item_four.title") }}
          </router-link>
        </li>
      </ul>
    </div>
  </div>
</template>


<script>
import { mapGetters } from "vuex";

export default {
  name: "SettingsSidebar",
  data: () => ({
    developer: "",
  }),
  // Map Getters
  computed: {
    ...mapGetters("auth", ["user"]),
  },
  created() {
    this.developer = this.user.roles.includes("developer");
  },
  methods: {},
};
</script>
