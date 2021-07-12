<template><div class="container">
	<div class="columns is-centered">
		<div class="column">
			<table class="table">
				<thead>
					<tr>
						<th>Initiative</th>
						<th>year</th>
						<th>Theme</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="initiative in table">
						<td>{{initiative.initiative}}</td>
						<td>{{initiative.year}}</td>
						<td>{{initiative.theme}}</td>
						<td><a @click="submit='Update';tupple=initiative"><i class="fas fa-edit"></i></a></td>
						<td><a @click="tupple=initiative;handle('delete')"><i class="fas fa-trash"></i></a></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="column">
			<div class="field">
			  <label class="label">Initiative</label>
			  <div class="control">
				<input v-model="tupple.initiative" class="input" type="text">
			  </div>
			</div>
			<div class="field">
			  <label class="label">Year</label>
			  <div class="control">
				<input v-model="tupple.year" class="input" type="number">
			  </div>
			</div>
			<div class="field">
			  <label class="label">Theme</label>
			  <div class="control">
				<input v-model="tupple.theme" class="input" type="text">
			  </div>
			</div>
			<div class="field is-grouped">
			  <div class="control">
				<button @click="handle('put')" class="button is-link">{{submit}}</button>
			  </div>			  
			  <div class="control">
				<button @click="tupple={};submit='Add'" class="button is-light">Reset</button>
			  </div>
			</div>
		</div>
	</div>
	
</div></template>
<script>
import axios from 'axios';
export default {
	props:['table'],
	data() {
        return {
        	tupple:{},
        	submit:'Add'
        }
    },
    methods: {
    	handle(crud){
    		axios.get('/admin/initiatives/'+crud,{
    			params:this.tupple
    		})
    		.then(function (response) {
    			location.reload()
			})
			.catch(function (error) {
				console.log(error);
			})
			.then(function () {
			});
    	}
    },
    created() {
    },
    mounted() {
    }	
};
</script>
