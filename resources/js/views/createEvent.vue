
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
                    <form v-on:submit.prevent="createEvent">
                        <h3>Create an Event</h3>
                        <div class="form-wrapper">
                            <label for="title"> Title </label>
                            <input type="text" name="title" id="title" v-model="title" placeholder="Enter title of event" class="form-control" required>
                        </div>

                        <div class="form-wrapper">
                            <label for="description">Description</label>
                            <input type="text" name="description" id="description" v-model="description" placeholder="Enter Description" class="form-control" required>
                        </div>

                        <div class="form-wrapper">
                            <label for="youtube_url">Youtube Link (Valid URL)</label>
                            <input type="url" name="youtube_url" id="youtube_url" v-model="youtube_url" placeholder="Enter valid Youtube URL" class="form-control" required>
                        </div>
                        <button type="submit" class="mt-3">CREATE &nbsp;&nbsp;<span v-if="showSpinner" class="fa fa-spin fa-spinner"></span> </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
export default {
    name: "CreateEvent",
    data() {
        return {
            title: "",
            description: "",
            youtube_url: '',
            showSpinner: false,
        };
    },
    methods: {
        async createEvent() {
            if (this.title && this.description && this.youtube_url) {
                this.showSpinner = true;
                let guid = Math.random().toString(36).substring(7);

                let url = `https://api-us.cometchat.io/v2.0/groups`;
                let data = {
                    guid,
                    name: `${this.title}`,
                    type: 'public',
                };
                try {
                    const eventDetails = await fetch(url, {
                        method: 'POST',
                        headers: new Headers({
                            appid: process.env.MIX_COMMETCHAT_APP_ID,
                            apikey: process.env.MIX_COMMETCHAT_REST_API_KEY,
                            'Content-Type': 'application/json'
                        }),
                        body: JSON.stringify(data),
                    });
                    const eventJson = await eventDetails.json();
                    console.log('New Event', eventJson);

                    // send event to the server
                    this.saveEventToDB(guid,this.title, this.description, this.youtube_url);
                } catch (error) {
                    console.log('Error', error);
                }
            } else {
                alert("Please fill all the fields")
            }
        },
        redirect() {
            window.location.href = '/home';
        },
        saveEventToDB(guid, title, description, youtube_url) {
            axios.post(`http://localhost:8000/create`, {guid, title, description, youtube_url})
                .then(response => {
                    console.log("Event created successfully", response);
                    this.redirect();
                }).catch(error => {
                console.log(error.response.data.message);
            })
        }
    }
};
</script>
