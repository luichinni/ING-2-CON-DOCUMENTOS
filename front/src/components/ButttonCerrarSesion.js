function onClick(){
    localStorage.clear();
    window.location.reload();
}
export function ButtonCerrarSesion (){
    return<button onClick={() => onClick()}>
                Cerrar Sesion
          </button>
}