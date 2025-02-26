export function request(url,method='GET',body=null,heads={}){
    let myInit = {
        method: method,
        headers: heads,
    };

    if(['POST','PATCH','PUT'].indexOf(method) !== -1){
        if(!body) return console.error('o paramentro body, nao foi enviado!')
        myInit['body'] = body
    }
   
    let velue_return = fetch(url,myInit)
    .then(response => {
        if (!response.ok) {
          console.log("Network response was not ok.");
        }
        
       return response.json();
                },
    )
    .catch(error => console.error(error));

    return velue_return;
}