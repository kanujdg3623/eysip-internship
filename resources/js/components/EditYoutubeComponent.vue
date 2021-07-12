<template><div>
	<div class="container">
<!--Edit Video-->
		<div class="modal" v-bind:class="{'is-active': popup}">
			<div class="modal-background"></div>
				<div class="modal-card">
					<header class="modal-card-head">
						<p class="modal-card-title">Update Video</p>
						<button class="delete" aria-label="close" @click="togglePopup"></button>
					</header>
					<section class="modal-card-body">
						<div class="field">
							<label class="label">Title</label>
							<div class="control">
								<textarea v-model="video.title" class="textarea" rows='2'></textarea>
							</div>
						</div>
						<div class="field">
							<label class="label">Description</label>
							<div class="control">
								<textarea v-model="video.description" class="textarea" rows='5'></textarea>
							</div>
						</div>
						<button @click="youtubeApi">
				        	<figure class="image">
								<img src="/yt_logo_rgb_light.png" style="height:32px;width:auto;">
							</figure>
						</button>
						<br><br>
						
						<div class="field">
							<label class="label">Initiative</label>
							<div class="control">
								<input v-model="video.initiative" class="input">
							</div>
						</div>
						<div class="field">
							<label class="label">Year</label>
							<div class="control">
								<input v-model="video.year" class="input">
							</div>
						</div>
					</section>
					<footer class="modal-card-foot">
						<button class="button is-success" @click="saveVideo('e')">Save changes</button>
						<button class="button" @click="togglePopup">Cancel</button>
					</footer>
				</div>
			<button class="modal-close is-large" aria-label="close" @click="togglePopup"></button>
		</div>
<!--Add Video-->
		<div class="modal" v-bind:class="{'is-active': addPopup}">
			<div class="modal-background"></div>
				<div class="modal-card">
					<header class="modal-card-head">
						<p class="modal-card-title">Add Video</p>
						<button class="delete" aria-label="close" @click="addPopup=!addPopup"></button>
					</header>
					<section class="modal-card-body">
						<div class="field">
							<label class="label">Video link</label>
							<div class="control">
								<input v-model="video.videoId" class="input">
							</div>
						</div>
						<button @click="youtubeApi">
				        	<figure class="image">
								<img src="/yt_logo_rgb_light.png" style="height:32px;width:auto;">
							</figure>
						</button>
						<br><br>
						<div class="field">
							<label class="label">Title</label>
							<div class="control">
								<textarea v-model="video.title" class="textarea" rows='2'></textarea>
							</div>
						</div>
						<div class="field">
							<label class="label">Description</label>
							<div class="control">
								<textarea v-model="video.description" class="textarea" rows='5'></textarea>
							</div>
						</div>
						<div class="field">
							<label class="label">Initiative</label>
							<div class="control">
								<input v-model="video.initiative" class="input">
							</div>
						</div>
						<div class="field">
							<label class="label">Year</label>
							<div class="control">
								<input v-model="video.year" class="input">
							</div>
						</div>
					</section>
					<footer class="modal-card-foot">
						<button class="button is-success" @click="saveVideo('a')">Save changes</button>
						<button class="button" @click="addPopup=!addPopup">Cancel</button>
					</footer>
				</div>
			<button class="modal-close is-large" aria-label="close" @click="addPopup=!addPopup"></button>
		</div>
<!--Stream Video-->	
		<div class="modal" v-bind:class="{'is-active': streamPopup}">
			<div class="modal-background"></div>
				<div class="modal-content">
                    <figure class="image is-4by3">
                        <iframe
                            class="has-ratio"
                            :src=" 'https://www.youtube.com/embed/' + video.videoId "
                            frameborder="0"
                            allowfullscreen
                        >
                        </iframe>
                    </figure>
				</div>
			<button class="modal-close is-large" aria-label="close" @click="streamPopup=!streamPopup;video={}"></button>
		</div>
<!--Add and grid-list icons-->
		<div class="columns is-centered">
			<div class="column is-12">
				<div class="is-pulled-left">
					<a class="button is-large is-info" @click="addModal">
						Add New
					</a>
				</div>
				<div class="is-pulled-right">
					<a class="button is-large" @click="grid=!grid">
						<span class="icon is-medium">
							<i class="fas fa-lg" v-bind:class="{'fa-th': grid,'fa-list':!grid}"></i>
						</span>
					</a>
				</div>
			</div>
		</div>
<!--Filter Videos-->
		<div class="columns is-gapless">
			<div class="column is-5">
				<input class="input is-rounded is-primary" type="text" placeholder="Initiative" v-model="query.initiative">
			</div>
			<div class="column is-5">
				<input class="input is-rounded is-primary" type="number" placeholder="year" min="2009" v-model="query.year">
			</div>
			<div class="column is-2">
				<button class="button is-fullwidth is-rounded is-primary" @click="loadVideos('1')">Search</button>
			</div>
		</div>
<!--Page Navigation-->
		<nav v-if="pagination.last_page>1" class="pagination is-centered" role="navigation" aria-label="pagination">
			<Paginate
				:page-count="Number(pagination.last_page)"
				:click-handler="loadVideos"
				:prev-text="'Back'"
				:next-text="'Next'"
				:container-class="'is-centered pagination-list'"
				:page-link-class="'pagination-link'"
				:prev-link-class="'pagination-previous'"
				:next-link-class="'pagination-next'"
				:break-view-link-class="'pagination-ellipsis'"
				:active-class="'pagination-link is-current'"
				>
			</Paginate>
		</nav>
<!--List View-->
		<table v-if="grid" class="table is-fullwidth">
			<thead>
				<tr>
					<th>Video Id</th>
					<th>Initiative</th>
					<th>Title</th>
					<th>year</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="video in videos">
					<td>{{video.videoId}}</td>
					<td>{{video.initiative}}</td>
					<td>{{video.title}}</td>
					<td>{{video.year}}</td>
					<td><a @click="editPopup(video)"><i class="fas fa-edit"></i></a></td>
					<td><a @click="deleteVideo(video.videoId)"><i class="fas fa-trash"></i></a></td>
				</tr>
			</tbody>
		</table>
<!--Grid View-->
		<div v-else class="columns is-multiline">
			<div class="column is-one-quarter" v-for="video in videos">
				<div class="card">
					<div class="card-image">
						<figure class="image is-4by3">
							<img @click="streamVideo(video)" :src="'https://img.youtube.com/vi/' + video.videoId + '/0.jpg'">
						</figure>
					</div>
					<div class="card-content" style="height:150px;">
						<p class="subtitle is-6">{{video.title}}</p>
						<p class="title is-5">{{video.initiative}} {{video.year}}</p>
					</div>
					<footer class="card-footer">
						<a class="card-footer-item" @click="editPopup(video)">Edit</a>
						<a class="card-footer-item" @click="deleteVideo(video)">Delete</a>
						<a class="card-footer-item" v-bind:href="'/admin/analytics?id='+video.videoId+'&title='+video.title">Stats</a>
					</footer>
				</div>
			</div>
		</div>
				
	</div>
</div></template>
<script>
import Paginate from 'vuejs-paginate';
import VueFuse from "vue-fuse";
import axios from 'axios';
export default {
    components: {
        VueFuse,
        Paginate
    },
    data() {
        return {
        	videos:[],
        	video:{},
        	pagination: {},
        	popup: false,
        	addPopup: false,
        	grid: false,
        	streamPopup: false,
        	query:{
        		initiative:'',
        		year:''
        	}
        };
    },
    methods: {
    	loadVideos(page){
    		this.query.initiative=this.query.initiative? this.query.initiative:'%';
    		this.query.year=this.query.year?this.query.year:'%';
    		var vm=this;
    		var page_url='/admin/youtube-videos?page='+page;
    		axios.get(page_url,{
    			params:this.query
    		})
    		.then(function (response) {
    			return response.data;   						
			})
			.then(function (response){
    			if(vm.query.initiative==='%') vm.query.initiative='';
    			vm.videos=response.data;
    			vm.makePagination(response);
			})
			.catch(function (error) {
				console.log(error);
			})
			.then(function () {
			});
    	},
    	makePagination(res){
    		let pagination={
    			current_page: res.current_page,
    			last_page: res.last_page,
    		}
    		this.pagination=pagination;
    	},
    	togglePopup(){
    		this.popup=!this.popup;
    	},
    	editPopup(video){
    		this.togglePopup();
    		this.video={...video};
    	},
    	addModal(){
    		this.addPopup=!this.addPopup;    		
        	this.video={
        		published_at:'',
        		videoId:'',
        		title:'',
        		description:'',
        		initiative:'',
        		year:''
        	}
    	},
    	saveVideo(popup){
    		var vm=this;
    		axios.put('/admin/youtube-update',{
    			params:this.video
    		})
    		.then(function (response) {
    			vm.loadVideos(vm.pagination.current_page);
			})
			.catch(function (error) {
				console.log(error);
			})
			.then(function () {				
    			if(popup=='a')
    				vm.addPopup=!vm.addPopup;
    			else
    				vm.togglePopup();
			});
    	},
    	deleteVideo(video){
    		var vm=this;
    		if(prompt('Are you sure, you want to DELETE ['+video.title+'] ?\nTo confirm enter the title below.')===video.title){
				axios.delete('/admin/youtube-delete',{
					params:{
						videoId:video.videoId
					}
				})
				.then(function(response){
					vm.loadVideos(vm.pagination.current_page);
					return response.data;
				})
				.then(function(response){
					console.log(response);
				})
				.catch(function (error) {
					console.log(error);
				})
				.then(function () {				
				});
			}
    	},
    	youtubeApi(){
    		var vm=this;
    		axios.get('https://www.googleapis.com/youtube/v3/videos',{
    			params:{
    				part:'snippet',
    				id: this.video.videoId,
    				key:process.env.MIX_GOOGLE_API_KEY
				}
    		})
    		.then(function (response) {
    			return response.data;   						
			})
			.then(function (response){
				if(response.items.length) {
					if(response.items[0].snippet.channelTitle==='e-Yantra'){
						vm.video.title=response.items[0].snippet.title;
						vm.video.description=response.items[0].snippet.description;
						vm.video.published_at=response.items[0].snippet.publishedAt;
					} else alert('Only e-Yantra channel Videos');
				} else alert('invalid video id');
			})
			.catch(function (error) {
				console.log(error);
			})
			.then(function () {
				
			});
    	},
    	streamVideo(video){
    		this.video=video;
    		this.streamPopup=!this.streamPopup;
    	}
    },
    created() {
    },
    mounted() {
    	this.loadVideos();
    }	
};
</script>
