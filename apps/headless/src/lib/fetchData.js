import axios from 'axios';

function graphQlFetchData(component, query, variables) {

    component.loading = true;
    component.error = component.data = null;


    var apiUrl = getApiUrl('/graphql', component.$route);

    axios.post(apiUrl,
        {
            query: query,
            variables: variables
        }
        )
        .then(response => {
            // console.log(response)
            component.loading = false;
            component.data = response.data.data;
        })
        .catch(error => {
            component.loading = false;
            component.error = error;
        })
    ;
}

function getApiUrl(apiUrl, route) {
    var token = route.query.token;
    if (token) {
        apiUrl += '?token=' + token;
    } else {
        let uri = window.location.search.substring(1);
        let params = new URLSearchParams(uri);
        token = params.get("token");
        if (token) {
            apiUrl += '?token=' + token;
        }
    }

    return apiUrl;
}


export {graphQlFetchData}
