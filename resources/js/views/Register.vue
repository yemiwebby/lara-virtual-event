<template>
    <div class="login-page">
        <div class="login">
            <div class="register-container auth-container">
                <div class="register-image-column">
                    <div class="image-holder">
                        <img src="#" alt="">
                    </div>
                </div>

                <div class="register-form-column">
                    <form v-on:submit.prevent="registerAppUser">
                        <h3>Create an Account</h3>
                        <div class="form-wrapper">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" v-model="name" placeholder="Enter your name" class="form-control" required>
                        </div>

                        <div class="form-wrapper">
                            <label for="email">Email</label>
                            <input type="text" name="email" id="email" v-model="email" placeholder="Enter your email" class="form-control" required>
                        </div>

                        <div class="form-wrapper">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" v-model="password" placeholder="Enter your password" class="form-control" required>
                        </div>

                        <div class="form-wrapper">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" v-model="password_confirmation" placeholder="Re-enter password" class="form-control" required>
                        </div>

                        <div class="form-wrapper">
                            <label for="avatar_url">Avatar (Optional)</label>
                            <input type="url" name="avatar_url" id="avatar_url" v-model="avatar_url" placeholder="Enter valid Avatar URL" class="form-control">
                        </div>
                        <button type="submit" class="mt-3">SIGN UP &nbsp;&nbsp;<span v-if="showSpinner" class="fa fa-spin fa-spinner"></span> </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            name: "",
            email: "",
            password: '',
            password_confirmation: '',
            showSpinner: false,
            avatar_url: "",
        };
    },
    methods: {
        registerAppUser() {
            if (this.name && this.email && this.password && this.password_confirmation) {
                this.showSpinner = true;
                if (this.password && this.password_confirmation) {
                    let data = {
                        name: this.name,
                        email: this.email,
                        password: this.password,
                        password_confirmation: this.password_confirmation,
                        avatar_url: this.avatar_url
                    };
                    axios.post(`http://localhost:8000/register`, data)
                        .then(response => {
                            console.log(response.data.status);
                            if (response.data.status) {
                                this.createUserOnCometChat(this.name, this.avatar_url);
                            }
                            this.showSpinner = false;
                        }).catch(error => {
                        console.log(error.response.data.message);
                        this.showSpinner = false;
                    })
                }
            }
        },
        redirect() {
            window.location.href = '/login';
        },
        async createUserOnCometChat(name, avatar = "https://picsum.photos/200/300.jpg") {
            let url = `https://api-us.cometchat.io/v2.0/users`;
            let data = {
                uid: name.replace(/\s/g, '').toLowerCase(),
                name,
                avatar
            };
            try {
                const userDetails = await fetch(url, {
                    method: 'POST',
                    headers: new Headers({
                        appid: process.env.MIX_COMMETCHAT_APP_ID,
                        apikey: process.env.MIX_COMMETCHAT_REST_API_KEY,
                        'Content-Type': 'application/json'
                    }),
                    body: JSON.stringify(data),
                });
                const userJson = await userDetails.json();
                console.log('New User', userJson);
                await this.createAuthTokenAndSaveForUser(data.uid);
            } catch (error) {
                console.log('Error', error);
            }
        },
        async createAuthTokenAndSaveForUser(uid) {
            let url = `https://api-us.cometchat.io/v2.0/users/${uid}/auth_tokens`;
            try {
                const tokenDetails = await fetch(url, {
                    method: 'POST',
                    headers: new Headers({
                        appid: process.env.MIX_COMMETCHAT_APP_ID,
                        apikey: process.env.MIX_COMMETCHAT_REST_API_KEY,
                        'Content-Type': 'application/json'
                    }),
                });
                const tokenJSON = await tokenDetails.json();
                console.log(tokenJSON);
                // await this.addUserToAGroup(uid);
                this.sendTokenToServer(tokenJSON.data.authToken, this.email);
            } catch (error) {
                console.log('Error Token', error);
            }
        },
        sendTokenToServer(token, email) {
            axios.post(`http://localhost:8000/update/token`, {token, email})
                .then(response => {
                    console.log("Token updated successfully", response);
                    this.redirect();
                }).catch(error => {
                console.log(error.response.data.message);
            })
        }
    }
};
</script>

