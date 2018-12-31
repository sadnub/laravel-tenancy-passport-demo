export const ChangesValidationResponse = {
	methods: {
		getErrorsFromResponse(response){
      if (response.status === 422){

      	if (response.data.errors){
        
	        for(let key in response.data.errors){
	        
	          this.errors.add({field: key, msg: response.data.errors[key]})
	        }
	    }
      }
    }
	}
}