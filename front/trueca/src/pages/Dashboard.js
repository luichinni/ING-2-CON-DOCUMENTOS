import Tarjeta from './publicaciones/Tarjeta';
import { ListaDePublicaciones } from './publicaciones/ListaDePublicaciones';
import '../HarryStyles/Publicaciones.css';

const ListarPublis = () => {
  
  if (ListaDePublicaciones.length === 0){
    return <h1 className="SinPubli">¡No hay publicaciones disponibles en este momento!</h1>;
  }
  
  const filasDePublicaciones = [];
  for (let i = 0; i < ListaDePublicaciones.length; i += 3) {
    filasDePublicaciones.push(ListaDePublicaciones.slice(i, i + 3));
  }
  
  return (
    <div className="ListadoPublis">
      {filasDePublicaciones.map((fila, index) => (
        <div key={index} className="fila">
          {fila.map(publicacion => (
            <div key={publicacion.ID} className="publicacion">
              <Tarjeta publicacion={publicacion} />
            </div>
          ))}
        </div>
      ))}
    </div>
  );
}

export default ListarPublis;
