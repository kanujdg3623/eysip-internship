<template>
    <div class="container">
        <div class="columns">
            <div class="column is-7">
                <div class="card">
                    <div id="stream" class="card-image">
                        <figure class="image is-16by9">
                            <video
                                @contextmenu.prevent
                                class="has-ratio"
                                id="CourseVideoPlayer"
                                controlsList="nodownload"
                                controls
                            >
                                <source
                                    :src="video.video_url"
                                    type="video/mp4"
                                />
                                Your browser does not support the video tag.
                            </video>
                        </figure>
                    </div>
                    <div class="card-content">
                        <div class="media">
                            <div class="media-left">
                                <figure class="image is-48x48">
                                    <img
                                        src="/eyantra_logo.png"
                                        alt="Placeholder image"
                                    />
                                </figure>
                            </div>
                            <div class="media-content">
                                <p class="title is-4">{{ video.name }}</p>
                            </div>
                        </div>

                        <div class="content">
                            {{ video.description }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="column is-1"></div>
            <div class="column is-4">
                <div class="field">
                    <p class="control has-icons-left">
                        <VueFuse
                            placeholder="Search from Keyword"
                            event-name="results"
                            :list="course_videos"
                            :keys="['name']"
                            class="input is-rounded is-fullwidth is-success"
                        />
                        <span class="icon is-small is-left">
                            <i class="fas fa-search"></i>
                        </span>
                    </p>
                </div>
                <div class="scroll">
                    <div
                        class="card"
                        v-for="video in results"
                        v-bind:key="video.id"
                    >
                        <div class="card-content">
                            <div class="media">
                                <div class="media-left">
                                    <figure class="image is-48x48">
                                        <img
                                            src="https://bulma.io/images/placeholders/128x128.png"
                                            alt="Placeholder image"
                                        />
                                    </figure>
                                </div>
                                <div class="media-content">
                                    <a
                                        @click="changeStream(video)"
                                        class="subtitle is-6"
                                        >{{ video.name }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import VueFuse from "vue-fuse";
import axios from "axios";
export default {
    props: ["course_videos"],
    data() {
        return {
            video: {},
            results: []
        };
    },
    components: {
        VueFuse
    },
    methods: {
        changeStream(video) {
            this.video = video;
            this.getAccessToken(this.video);
        },
        getAccessToken(video) {
        	var vm=this;
        	console.log(`/course/${video.course_id}/${video.id}/token`);
            axios
                .get(`/admin/course/${video.course_id}/${video.id}/token`)
                .then(function(response) {
                	return response.data;
                })
                .then(function(response){
                	vm.video.video_url=response;
                	document.getElementById('CourseVideoPlayer').load();
                })
                .catch(function(error) {
                    console.log(error);
                })
                .then(function() {});
        }
    },
    created() {
        this.$on("results", results => {
            this.results = results;
        });
    },
    mounted() {
        this.video = this.course_videos[0];
        this.getAccessToken(this.video);
    }
};
</script>
