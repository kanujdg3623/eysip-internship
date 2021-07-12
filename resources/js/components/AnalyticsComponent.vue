<template>
    <div class="container">
    	<div class="columns is-marginless is-centered">
    		<div class="column is-2">
    			<div class="field">
    				<label class="label">Start Date</label>
	    			<div class="control">
	    				<input v-model="start_date" class="input" type="date" :max="this.end_date">
	    			</div>
    			</div>
    		</div>
    		<div class="column is-2">
    			<div class="field">
    				<label class="label">End Date</label>
	    			<div class="control">
	    				<input v-model="end_date" class="input" type="date" :max="this.end_date">
	    			</div>
    			</div>
    		</div>
    		<div class="column is-2">
    			<div class="field">
    				<label class="label">Dimension</label>
	    			<div class="select">
	    				<select v-model="dimension">
	    					<option value="day">Daily</option>
	    					<option value="month">Monthly</option>
	    					<option value="country">Country</option>
	    				</select>
	    			</div>
    			</div>
    		</div>
    		<div class="column is-2">
				<div style="margin-top:30px;" class="control">
					<button @click="getAnalytics()" class="button is-success">Refresh</button>
				</div>
			</div>
    	</div>
    	<div v-if="id">
	    	<div class="tabs is-centered">
				<ul>
					<li v-bind:class="{'is-active':active==1}"><a @click="active=1;showAnalytics(1,'Estimated Minutes Watched')">Estimated Minutes Watched</a></li>
					<li v-bind:class="{'is-active':active==2}"><a @click="active=2;showAnalytics(2,'views')">Views</a></li>
					<li v-bind:class="{'is-active':active==3}"><a @click="active=3;showAnalytics(3,'likes')">Likes</a></li>
				</ul>
			</div>
			<div style="width: 100%; overflow-x: auto">
				<div :style="{width: setWidth, height: '600px' , position:'relative'}">
					<canvas ref="myChart"></canvas>
				</div>
			</div>
    	</div>
    	<div v-else class="columns is-marginless is-centered">
		    <div class="column is-6">
		        <nav class="card">
		        	<div class="card-content">        		
				    	<table class="table is-fullwidth">
				    		<thead>
				    			<th> Top 20 Videos</th>
				    			<th>Estimated Minutes Watched</th>
				    			<th>Views</th>
				    			<th>Likes</th>
				    		</thead>
				    		<tbody>
				    			<tr v-for="video in analytics">
				    				<td><a v-if="" :href="'/admin/analytics?id='+video[0]+'&title='+video[4]">{{video[4]}}</a></td>
				    				<td>{{video[1]}}</td>
				    				<td>{{video[2]}}</td>
				    				<td>{{video[3]}}</td>
				    			</tr>
				    		</tbody>
				    	</table>
		        	</div>
	        	</nav>
        	</div>
        	<div class="column is-6">
		    	<div class="tabs is-centered">
					<ul>
						<li v-bind:class="{'is-active':active==1}"><a @click="active=1;showAnalytics(1,'Estimated Minutes Watched')">Estimated Minutes Watched</a></li>
						<li v-bind:class="{'is-active':active==2}"><a @click="active=2;showAnalytics(2,'views')">Views</a></li>
						<li v-bind:class="{'is-active':active==3}"><a @click="active=3;showAnalytics(3,'likes')">Likes</a></li>
					</ul>
				</div>        		
		    	<canvas ref="myChart" width="400" height="400"></canvas>
        	</div>
    	</div>
    </div>
</template>

<script>
	import axios from 'axios'
    export default {
    	props:['id','title'],
    	data() {
            return {
            	analytics:[],
            	active:1,
            	start_date:'',
            	end_date:'',
            	dimension:'day',
            	ctx:'',
            	setWidth:''
            }
        },
        methods: {
        	clearCanvas(){
        		let myChart=this.$refs.myChart;
				this.ctx.clearRect(0,0,myChart.width,myChart.height);     
				let len=this.analytics.length;
				this.setWidth=len <=33?'':(len * 40) + 'px';
        	},
        	async showAnalytics(num,met){
        		var vm=this;
        		let x=[], y=[];
        		for(let i=0,len=this.analytics.length;i<len;i++){
        			x.push(this.analytics[i][0]);
        			y.push(this.analytics[i][num]);
        		}
        		await this.clearCanvas();
		        var myChart = new Chart(this.ctx, {
				type: 'line',
				data: {
		    		labels: x,
		    		datasets: [{
		    				label: met,
		        			data: y,
		        			fill:false,
		        			borderColor: 'rgba(255, 99, 132)'
		    			}]
					},
					options:{
						maintainAspectRatio:vm.id?false:true,
						scales: {
							xAxes: [{
							  scaleLabel: {
								display: true,
								labelString: ''
								}
							}]
						},
						elements: {
						line: {
						    tension: 0
							}
						}
					}
				});
        	},
        	top20videos(){
				axios('/admin/get-analytics',{
					params: {
						'startDate' : '2009-01-01',
						'endDate' : this.end_date,
						'ids' : 'channel==MINE',
						'metrics' : 'estimatedMinutesWatched,views,likes',
						'dimensions' : 'video',
						'maxResults' : 20,
						'sort' : '-estimatedMinutesWatched'
					}
				})
				.then(res => res.data)
				.then(res => {
					this.analytics=res;
					for(let i=0,len=this.analytics.length;i<len;i++){
						axios.get('https://www.googleapis.com/youtube/v3/videos?part=snippet&id='+res[i][0]+'&key='+process.env.MIX_GOOGLE_API_KEY)
							.then(response=>{
								if (response.data.items[0]) this.analytics[i].push(response.data.items[0].snippet.title);
								else this.analytics[i].push('private video');
							})
					}
					this.showAnalytics(1,'Estimated Minutes Watched');
				})
				.catch(err => console.log(err));
        	},
        	getAnalytics(){
        		if(this.dimension=='month'){
        			this.start_date=this.start_date.substring(0,8)+'01';
        			this.end_date=this.end_date.substring(0,8)+'01';
        		}
        		var vm=this;
		    	axios('/admin/get-analytics',{
					params: {
						'startDate' : this.start_date,
						'endDate' : this.end_date,
						'ids' : 'channel==MINE',
						'metrics' : 'estimatedMinutesWatched,views,likes',
						'dimensions' : this.dimension,
						'filters' : 'video=='+vm.id
					}
				})
				.then(res => res.data)
				.then(res => {
					vm.analytics=res;
					vm.showAnalytics(1,'Estimated Minutes Watched');
				})
				.catch(err => console.log(err));
		    },
        },
        mounted() {        	
	    	this.ctx = this.$refs.myChart.getContext('2d');
        	let date=new Date();
        	this.start_date=new Date(date.getTime()-(28*24*60*60*1000)).toISOString().substring(0,10);
        	this.end_date=date.toISOString().substring(0,10);
        	if(this.id)
        		this.getAnalytics();
        	else
        		this.top20videos();
        },
        created(){
        }
    }
</script>
