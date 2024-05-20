import React from 'react';
import ListarPublis from '../pages/publicaciones/ListarPublis';
import '../HarryStyles/Publicaciones.css'; 

const Dashboard = () => {
  return (
    <div className='dashboard'>
      <banner className='banner'>
        <h1>Bienvenido al Dashboard</h1>
        <p>Gestiona tus publicaciones aquí</p>
      </banner>
      
      <ListarPublis />
    </div>
  );
}

export default Dashboard;




/*
  const filasDePublicaciones = [];
  for (let i = 0; i < ListaDePublicaciones.length; i += 3) {
    filasDePublicaciones.push(ListaDePublicaciones.slice(i, i + 3));
  }
  
  return (
    <div className="Contents">
      {ListaDePublicaciones.map(publicacion => (
        <div key={publicacion.ID} className="Publi-Div">
          <Tarjeta publicacion={publicacion} />
        </div>
      ))}
    </div>
  );
  
  
}

export default ListarPublis;*/
/*import Tarjeta from './publicaciones/Tarjeta';
import { ListaDePublicaciones } from './publicaciones/ListaDePublicaciones';
import '../HarryStyles/Publicaciones.css';

const ListarPublis = () => {
  
  if (ListaDePublicaciones.length === 0){
    return <h1 className="SinPubli">¡No hay publicaciones disponibles en este momento!</h1>;
  }
export default ListarPublis;*/
