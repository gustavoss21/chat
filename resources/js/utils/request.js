export function request(url,method='GET',body=null,heads={}){

    heads['X-CSRF-TOKEN'] = document.querySelector('meta[name=csrf-token]').content
    heads.Accept = 'application/json';
    heads.Origin = location.origin;

    let myInit = {
        method: method,
        // set cookie
        credentials : "same-origin",
        // credentials : 'include', // Importante para enviar cookies de sessão

        headers: heads,
    };
    console.log(myInit);

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