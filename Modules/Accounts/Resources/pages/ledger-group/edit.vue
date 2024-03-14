<template>
    <div>
        <!-- breadcrumbs Start -->
        <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
        <!-- breadcrumbs end -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            {{ $t('ledger_group.edit.form_title') }}
                        </h3>
                        <router-link :to="{ name: 'ledger-group' }" class="btn btn-secondary float-right">
                            <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
                        </router-link>
                    </div>
                    <!-- /.card-header -->

                    <!-- form start -->
                    <form role="form" @submit.prevent="updateLedgerGroup" @keydown="form.onKeydown($event)">
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="coa_id">{{ $t('common.group_type') }}
                                        <span class="required">*</span></label>
                                    <multiselect v-model="coa_account_id" :options="hotelItems" :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a Food category" class="form-control" label="name" track-by="name" :class="{ 'is-invalid': form.errors.has('coa_id') }"></multiselect>
                                    <has-error :form="form" field="coa_id" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="name">{{ $t('common.display_name') }}
                                        <span class="required">*</span></label>
                                    <input id="name" v-model="form.name" type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('name') }"
                                        name="name" :placeholder="$t('common.display_name_placeholder')" />
                                    <has-error :form="form" field="name" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="system_name">{{ $t('common.system_name') }}
                                        <span class="required">*</span></label>
                                    <input id="system_name" v-model="form.system_name" type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('system_name') }"
                                        name="system_name" :placeholder="$t('common.system_name_placeholder')" />
                                    <has-error :form="form" field="system_name" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="position">{{ $t('common.weight_age_position') }}</label>
                                    <input id="position" v-model="form.position" type="number"
                                        class="form-control" name="position" :placeholder="$t('common.weight_age_position_placeholder')" />
                                </div>
                            </div>
                        </div> 
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <v-button :loading="form.busy" class="btn btn-primary">
                                <i class="fas fa-save" /> {{ $t('common.save') }}
                            </v-button>
                            <button type="reset" class="btn btn-secondary float-right" @click="form.reset()">
                                <i class="fas fa-power-off" /> {{ $t('common.reset') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
  
  
<script>
import { mapGetters } from 'vuex'
import Multiselect from 'vue-multiselect'
import Form from 'vform'
export default {
    // middleware: ['auth', 'check-permissions'],
    metaInfo() {
        return { title: this.$t('ledger_group.edit.page_title') }
    },
    computed: {
        ...mapGetters('operations', ['items', "hotelItems"]),
    },
    components:{
        Multiselect
    },
    created() {
        this.getCoaListData()
    },
    data: () => ({
        breadcrumbsCurrent: 'ledger_group.edit.breadcrumbs_current',
        breadcrumbs: [
            {
                name: 'ledger_group.edit.breadcrumbs_first',
                url: 'home',
            },
            {
                name: 'ledger_group.edit.breadcrumbs_second',
                url: 'ledger-group',
            },
            {
                name: 'ledger_group.edit.breadcrumbs_active',
                url: '',
            },
        ],
        form: new Form({
            coa_id: '',
            name: '',
            system_name: '',
            position: '',
            cat_id: '',
        }),
        loading: true,
        coa_account_id: '',
    }),

    mounted() {
        this.getLegerGroupData()
    },
    methods: {
        // get category
        async getLegerGroupData() {
            const { data } = await this.form.get(window.location.origin + '/api/account/ledger/group/view/' + this.$route.params.slug)  
            this.form.cat_id = data.data.id
            this.form.name = data.data.name
            this.form.system_name = data.data.system_name
            this.form.position = data.data.position
            this.coa_account_id = data.data.ledger
        },

        async getCoaListData() {
            await this.$store.dispatch('operations/getHotelData', {
                path: '/api/account/coa'
            })
        },

        // update category
        async updateLedgerGroup() {
            this.form.coa_id = this.coa_account_id.id
            await this.form
                .post(
                    window.location.origin +
                    '/api/account/ledger/group/add'
                )
                .then((response) => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('ledger_group.edit.success_msg'),
                    })
                    this.$router.push({ name: 'ledger-group' })
                })
                .catch(() => {
                    toast.fire({
                        type: 'error',
                        title: this.$t('common.error_msg'),
                    })
                })
        },
    },
}
</script>
  
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style scoped>
:deep(.multiselect__tags) {
    min-height: 38px !important;
    border: none !important;
    padding: 4px 40px 0 4px !important;
}
:deep(.multiselect__placeholder){
    margin-bottom: 4px !important;
    padding-top: 4px !important;
}
:deep(.multiselect__single) {
    margin-bottom: 0px !important;
    margin-top: 4px !important;
}
:deep(.multiselect){
    width: auto;
    padding-bottom: 0px !important;
    padding-top: 0px !important;
    min-height: 38px !important;
}
</style>