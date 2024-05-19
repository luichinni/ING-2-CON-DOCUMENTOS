import Tarjeta from './Tarjeta';
import { ListaDePublicaciones } from './ListaDePublicaciones';

export function ListarPublis() {
  
  if (ListaDePublicaciones.length === 0){
    return <h1 className="text-white text-4xl font-bold text-center">No hay publicaciones</h1>;
  }
  
  const filasDePublicaciones = [];
  for (let i = 0; i < ListaDePublicaciones.length; i += 3) {
    filasDePublicaciones.push(ListaDePublicaciones.slice(i, i + 3));
  }
  
  return (
    <div className="container mx-auto px-4 py-8">
      {filasDePublicaciones.map((fila, index) => (
        <div key={index} className="flex justify-between">
          {fila.map(publicacion => (
            <div key={publicacion.ID} className="w-1/3 p-4">
              <Tarjeta publicacion={publicacion} />
            </div>
          ))}
        </div>
      ))}
    </div>
  );
}