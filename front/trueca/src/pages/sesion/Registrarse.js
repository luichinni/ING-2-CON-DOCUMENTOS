import { ButtonSubmit } from "../../components/ButtonSubmit";

const  Registrarse = () => {
return <>
	<h1>Trueca </h1>
	<div id="Registrarse">
		<br/>
		<p> Registrate para poder ofertar intercambios con los demás usuarios! </p>
		<form method="post">
			<label id="formtext" >Nombre </label>
			<br/>
			<input type="text" NameClass="registrarse" required />
			<br/>
			
			<label id="formtext" >Apellido </label>
			<br/>
			<input type="text" NameClass="registrarse" required /> 
			<br/>
			
			<label id="formtext" >Edad </label>
			<br/>
			<input type="text" NameClass="registrarse" required /> 
			<br/>
			
			<label id="formtext" >N° de DNI </label>
			<br/>
			<input type="text" NameClass="registrarse" required /> 
			<br/>
			
			<label id="formtext" >Email </label>
			<br/>
			<input type="text" NameClass="registrarse" required />
			<br/>
			
			<label id="formtext" >Telefono </label>
			<br/>
			<input type="text" NameClass="registrarse" /> 
			<br/>
			
			<label id="formtext" >Contraseña </label>
			<br/>
			<input type="text" NameClass="registrarse" required /> 
			<br/>
			
			<ButtonSubmit text="Registrarse" />
		</form>
	</div>
</>
}

export default Registrarse