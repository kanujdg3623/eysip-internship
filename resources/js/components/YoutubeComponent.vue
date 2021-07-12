<template>
	<div>
        <div class="container">
            <!--Theme Year cards-->
            <div v-if="cards.length">
                <div class="kards">
                    <div class="kard" v-for="card in cards">
                        <div class="kard-info">
                            <span class="kard-grad--video">Video</span>
                            <span>{{ card.year }}</span>
                        </div>

                        <div class="kard-heading">
                            <p target="_blank">{{ card.theme }}</p>
                        </div>

                        <div class="kard-tags">
                            <button
                                @click="fetchVideos(card.year)"
                                class="kard-tag"
                            >
                                Explore
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!--Initiative Year Title-->
            <section class="hero is-small">
                <div class="hero-body">
                    <h1 v-if="cards.length" class="title">
                        {{ this.initiative }}-{{ video.year }}
                    </h1>
                </div>
            </section>
            <!--Video, title and description-->
            <div class="columns">
                <div class="column is-7">
                    <div class="card">
                        <div class="card-image">
                            <figure class="image is-16by9">
                                <iframe
                                    class="has-ratio"
                                    :src="
                                        'https://www.youtube.com/embed/' +
                                            video.videoId
                                    "
                                    frameborder="0"
                                    allowfullscreen
                                >
                                </iframe>
                            </figure>
                        </div>
                        <div class="card-content">
                            <div class="media">
                                <div class="media-left">
                                    <figure class="image is-48x48">
                                        <img
                                            src="/eyantra_logo.png"
                                            alt="eyantra logo"
                                        />
                                    </figure>
                                </div>
                                <div class="media-content">
                                    <p class="title is-4">{{ video.title }}</p>
                                </div>
                            </div>
                        </div>
                        <pre class="subtitle is-6">
						{{ video.description }}
					</pre
                        >
                    </div>
                </div>
                <div class="column is-1"></div>
                <!--Keyword search-->
                <div class="column is-4">
                    <div class="field">
                        <p class="control has-icons-left">
                            <VueFuse
                                placeholder="Search from Keyword"
                                event-name="results"
                                :list="videos"
                                :keys="['title']"
                                class="input is-rounded is-fullwidth is-success"
                            />
                            <span class="icon is-small is-left">
                                <i class="fas fa-search"></i>
                            </span>
                        </p>
                    </div>
                    <!--Videos list-->
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
                                                :src="
                                                    'https://img.youtube.com/vi/' +
                                                        video.videoId +
                                                        '/0.jpg'
                                                "
                                                alt="thumbnail"
                                            />
                                        </figure>
                                    </div>
                                    <div class="media-content">
                                        <a
                                            @click="changeStream(video)"
                                            class="subtitle is-6"
                                            >{{ video.title }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table" style="margin-left:auto;margin-right:auto;">
		    	<body>
		    		<tr>
		    			<td align="bottom" style="vertical-align:bottom">
							<figure class="image">
						        <img src="/BIPED-ROBO.gif" />
						    </figure>
					    </td>
		    			<td>
							<figure class="image">
						        <img src="/BIPEDBOT_TENT_TRAN.png" />
						    </figure>
					    </td>
		    		</tr>
		    	</body>
		    </table>
        </div>
	</div>
</template>

<script>
import VueFuse from "vue-fuse";
import axios from "axios";
export default {
    props: ["initiative"],
    components: {
        VueFuse
    },
    data() {
        return {
            cards: [],
            videos: [],
            video: {},
            results: []
        };
    },
    methods: {
        loadCards() {
            let vm = this;
            axios
                .get("/videos/year", {
                    params: {
                        initiative: this.initiative
                    }
                })
                .then(function(response) {
                    if (response.data.length) vm.cards = response.data;
                })
                .catch(function(error) {
                    console.log(error);
                })
                .then(function() {
                    if (vm.cards.length) vm.fetchVideos(vm.cards[0].year);
                    else vm.fetchVideos("%");
                });
        },
        fetchVideos(year) {
            let vm = this;
            axios
                .get("/videos", {
                    params: {
                        initiative: this.initiative,
                        year: year
                    }
                })
                .then(function(response) {
                    if(response.data.length) {
                        vm.videos = response.data;
                        vm.video = vm.videos[0];
                    }
                    else alert('no videos available for '+vm.initiative+'-'+year);
                })
                .catch(function(error) {
                    console.log(error);
                })
                .then(function() {
                    document.title =
                        vm.initiative + (year == "%" ? "" : "-" + year);
                });
        },
        changeStream(video) {
            this.video = video;
        }
    },
    created() {
        this.$on("results", results => {
            this.results = results;
        });
    },
    mounted() {
        this.loadCards();
    }
};
</script>
