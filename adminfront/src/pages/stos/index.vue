<template>
    <div>
        <v-layout row wrap>
            <v-flex xs12 sm12 md12 lg12>
                <v-btn @click.native.stop="dialog.createS = true" color="success">Создать СТО</v-btn>
                <v-btn @click.native.stop="dialog.bindUser = true" color="info">Привязать пользователя</v-btn>
                <!-- Create new sto modal -->
                <v-dialog v-model="dialog.createS" max-width="500">
                    <form @submit.prevent="createSTO" data-vv-scope="create-sto-form">
                        <v-card>
                            <v-card-title class="headline">Создать СТО</v-card-title>
                            <v-card-text>
                                <v-layout>
                                    <v-flex xs12>
                                        <v-text-field type="text" v-model="newSTO.sto_name" name="sto_name" label="Название СТО" prepend-icon="build"                 
                                            v-validate="'required'" 
                                            data-vv-name="sto_name" data-vv-as='"Название СТО"' required
                                            :error-messages="errors.collect('sto_name')"
                                        ></v-text-field>
                                        <v-text-field hint="Например: +992 987641312" prepend-icon="phone"  v-model="newSTO.contact" name="contact" label="Контакт СТО"></v-text-field>

                                        <img class="logo-preview" :src="newSTO.logo.url" height="150" v-if="newSTO.logo.url" />
                                        <v-text-field label="Выберите логотип" @click="pickFile" v-model="newSTO.logo.name" prepend-icon="attach_file"></v-text-field>
                                        <input type="file" style="display: none" @change="onFilePicked" ref="image" accept="image/*">
                                    </v-flex>
                                </v-layout>
                            </v-card-text>
                            
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="blue darken-1" flat="flat" @click.native="dialog.createS = false">Закрыть</v-btn>
                                <v-btn color="green darken-1" :loading="loading.button" flat="flat" type="submit">Создать</v-btn>
                            </v-card-actions>
                    </v-card>
                    </form>
                </v-dialog>
                <!-- Bind user to sto modal -->
                <v-dialog v-model="dialog.bindUser" max-width="500">
                    <form @submit.prevent="bindUserToSTO" data-vv-scope="bind-user-form">
                        <v-card>
                            <v-card-title class="headline">Привязать пользователя</v-card-title>
                            <v-card-text>
                                <v-layout>
                                    <v-flex xs12>
                                        <v-select :items="users" v-model="bindUser.user_id" label="Выберите пользователя" prepend-icon="supervised_user_circle" persistent-hint
                                            name="user"
                                            v-validate="'required'" 
                                            :error-messages="errors.collect('user')"
                                            data-vv-name="type" data-vv-as='"Пользователь"'
                                        ></v-select>     

                                        <v-select :items="stos" v-model="bindUser.sto_id" label="Выберите СТО" prepend-icon="build" persistent-hint
                                            name="sto"
                                            v-validate="'required'" 
                                            :error-messages="errors.collect('sto')"
                                            data-vv-name="sto" data-vv-as='"СТО"'
                                        ></v-select>
                                    </v-flex>
                                </v-layout>
                            </v-card-text>  
                            
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="blue darken-1" flat="flat" @click.native="dialog.bindUser = false">Закрыть</v-btn>
                                <v-btn color="green darken-1" :loading="loading.button" flat="flat" type="submit">Привязать</v-btn>
                            </v-card-actions>
                        </v-card>
                    </form>
                </v-dialog>

                <v-snackbar :timeout="snackbar.timeout" :color="snackbar.color" v-model="snackbar.active">
                    {{ snackbar.text }}
                    <v-btn dark flat @click.native="snackbar.active = false">Закрыть</v-btn>
                </v-snackbar>
            </v-flex>
        </v-layout>    
        
        <!-- A list of stos -->
        <transition-group tag="v-layout" class="row wrap" name="slide-x-transition">     
            <v-flex xs12 sm4 md3 lg2 v-for="item in items" :key="item.id">
                <v-card>
                    <v-card-media height="150px">
                        <v-layout row justify-center align-center>
                        <v-flex xs6 sm6 md6 lg6>
                            <img v-if="item.logo" :src="`${assetURL}/${item.logo}`" :alt="`Логотип ${item.sto_name}`">
                            <img v-else src="/static/images/no-logo.png" alt="Нет логотипа">
                        </v-flex>
                        </v-layout>
                    </v-card-media> 
                    <v-divider></v-divider>           
                    <v-card-title primary-title>
                        <div>
                            <h3 class="title mb-0">{{ item.sto_name }}</h3>
                            <div v-if="item.contact">{{ item.contact }}</div>
                            <div v-else>Контакта нет</div>
                        </div>
                    </v-card-title>
                    <!-- <v-card-actions>
                        <v-btn flat color="orange">Изменить</v-btn>
                    </v-card-actions> -->
                </v-card>
            </v-flex>      
        </transition-group>

        <!-- Alert if there're no stos registered -->
        <v-layout v-if="alert.noSTOs">
            <v-flex>
                <v-alert outline transition="scale-transition" type="info" :value="true">
                    и одного СТО не зарегистрировано.
                </v-alert>
            </v-flex>
        </v-layout>
    </div>
</template>

<script>
import axios from '@/axios'
import config from '@/config'

export default {
    $_veeValidate: {
        validator: 'new'
    },
    computed: {
        assetURL() {
            return config.assetsURL;
        },
    },
    data() {
        return {
            // Controls
            selected: [],
            dialog: {
                createS: false,
                bindUser: false
            }, 
            loading: {
                table: false,
                button: false
            },    
            alert: {
                noSTOs: false
            },
            snackbar: {
                active: false,
                text: '',
                timeout: 5000,
                color: ''
            },
            bindUser: {
                user_id: null,
                sto_id: null
            },

            // API
            items: [],
            newSTO: {
                sto_name: '',
                contact: '',
                logo: {
                    name: '',
                    file: '',
                    url: ''
                },
            },
            users: [],
            stos: []
        }
    },
    methods: {
        fetchSTOs() {
            this.alert.noSTOs = false;
            axios.get('/admin/stos')
                .then(response => {                    
                    this.items = response.data;
                    this.items.map(sto => {
                        this.stos.push({
                            text: sto.sto_name,
                            value: sto.id
                        });                
                    });
                    if(this.items.length === 0) {
                    this.alert.noSTOs = true;
                    }
                })
                .catch(error => {
                    console.log(error);
                });
        },

        fetchUsers() {
            axios.get('/admin/users')
            .then(response => {
                if(response.status === 200) {
                let fullUsersInfo = response.data;

                fullUsersInfo.map(user => {
                    this.users.push({
                        text: user.fullname,
                        value: user.id
                    });                
                })
                }
            })
            .catch(error => console.log(error));
        },

        createSTO() {
            this.loading.button = true;
            this.$validator.validateAll('create-sto-form')
            .then(success => {
                if(success) {
                let formData = new FormData();
                formData.append('sto_name', this.newSTO.sto_name);
                formData.append('contact', this.newSTO.contact);
                formData.append('logo', this.newSTO.logo.file);

                axios.post('/admin/stos', formData)
                    .then(response => {
                        this.loading.button = false;
                        this.items.push(response.data.sto);
                        this.stos.push({
                            text: response.data.sto.sto_name,
                            value: response.data.sto.id
                        });
                        this.alert.noSTOs = false;
                        this.snackbar.color = 'success';
                        this.snackbar.text = response.data.message;
                        this.snackbar.active = true;
                    })
                    .catch(error => {
                        console.log(error);
                        this.loading.button = false;
                    });
                }
            })
        },

        bindUserToSTO() {
            this.loading.button = true;
            this.$validator.validateAll('bind-user-form')
                .then(success => {
                    if(success) {
                        axios.post(`/admin/stos/${this.bindUser.sto_id}/bind/${this.bindUser.user_id}`)
                            .then(response => {
                                if(response.status === 200) {
                                    this.loading.button = false;
                                    this.snackbar.color = 'success';
                                    this.snackbar.text = response.data.message;
                                    this.snackbar.active = true;
                                }
                            })
                            .catch(error => {
                                console.log(error);
                                this.loading.button = false;
                            });
                    } else {
                        this.loading.button = false;
                    }
                })
        },  

        pickFile () {
            this.$refs.image.click();
        },

        onFilePicked (e) {
            const files = e.target.files;
            if(files[0] !== undefined) {
            this.newSTO.logo.name = files[0].name;
            if(this.newSTO.logo.name.lastIndexOf('.') <= 0) {
                return
            }

            const fr = new FileReader();
            fr.readAsDataURL(files[0])
            fr.addEventListener('load', () => {
                this.newSTO.logo.url = fr.result;
                this.newSTO.logo.file = files[0];
                })
            } else {
                this.newSTO.logo.url = '';
                this.newSTO.logo.name = '';
                this.newSTO.logo.file = '';
            }
        }
    },
    created() {
        this.fetchSTOs();
        this.fetchUsers();
    }
}
</script>

<style>
  .logo-preview {
    display: block;
    margin: 0 auto;
  }
</style>
