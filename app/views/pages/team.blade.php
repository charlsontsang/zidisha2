@extends('layouts.master')

@section('page-title')
Meet the Team
@stop


@section('content')

<div class="page-header">
    <h1>Meet the Team</h1>
</div>

<div class="row info-page" style="padding-bottom: 30px !important;">
	<div class="col-sm-6">
		<p>HELLO. JAMBO. CIAO. NA NGA DEF. HOLA. BONJOUR.</p>
		<p>Greetings — from all corners of the globe! Our team members may not share time zones, but we do share a passion for building a person-to-person microlending community that overcomes the barriers of location and circumstance to help deserving, driven entrepreneurs all over the world.</p>
	</div>
	<div class="col-sm-6">
		<p>Some of us are full-time staff, but most of us are volunteers who either work with Zidisha from our own homes around the world or have actually relocated to developing countries to serve as ambassadors to current and prospective borrowers.</p>
		<p>Check out our <a href="{{ route('page:volunteer') }}">Volunteer</a> page to learn about our volunteer and internship opportunities.</p>
	</div>
</div>

<div class="team">
	<div class="row">
	    <div class="col-sm-4">
		    <ul class="img-list">
			  <li>
			    <img src="/assets/images/pages/team/julia.jpg" width="100%" style="border: 1px solid gray;" />
			    <span class="text-content"><span>
			    	<strong>Julia Kurnia</strong><br/>
			    	Co-founded the world's first microfinance institution built on capital raised from individuals over the internet. Developed grant projects in Africa for the US government before founding Zidisha. Enjoys practicing Indonesian martial arts.
			    </span></span>
			  </li>
			</ul>
	        <p><i class="fa fa-fw fa-user"></i><strong>Julia Kurnia</strong>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Director</p>
	        <p><i class="fa fa-fw fa-map-marker"></i>Sterling, Virginia&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://www.linkedin.com/profile/view?id=89682838"><i class="fa fa-fw fa-linkedin-square"></i>LinkedIn</a></p>
	    </div>
	    <div class="col-sm-4">
		    <ul class="img-list">
			  <li>
			    <img src="/assets/images/pages/team/mien.jpg" width="100%" />
			    <span class="text-content"><span>
			    	<strong>Mien De Graeve</strong><br/>
			    	Born in South Africa and grew up in Belgium. Moved to Burkina Faso to coordinate the Zidisha program in September 2012. Now runs her own sociocultural enterprise in the capital Ouagadougou while continuing to volunteer with Zidisha.
			    </span></span>
			  </li>
			</ul>
	        <p><i class="fa fa-fw fa-user"></i><strong>Mien De Graeve</strong>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Burkina Faso Ambassador</p>
	        <p><i class="fa fa-fw fa-map-marker"></i>Ouagadougou, Burkina Faso</p>
	        <p><a href="https://www.linkedin.com/profile/view?id=2169854"><i class="fa fa-fw fa-linkedin-square"></i>LinkedIn</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.mo.be/auteur/mien-de-graeve"><i class="fa fa-fw fa-rss-square"></i>Blog</a></p>
	    </div>
	    <div class="col-sm-4">
		    <ul class="img-list">
			  <li>
			    <img src="/assets/images/pages/team/paige.jpg" width="100%" />
			    <span class="text-content"><span>
			    	<strong>Paige Klunk</strong><br/>
			    	Divides her time between Senegal and the United States, where she is pursuing a master's degree in International Development Studies at Ohio University. Plays the balafon (West African xylophone) and follows West African local hip-hop scene.
			    </span></span>
			  </li>
			</ul>
	        <p><i class="fa fa-fw fa-user"></i><strong>Paige Klunk</strong>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Senegal Liaison</p>
	        <p><i class="fa fa-fw fa-map-marker"></i>Athens, Ohio</p>
	        <p><a href="https://www.linkedin.com/profile/view?id=89784658"><i class="fa fa-fw fa-linkedin-square"></i>LinkedIn</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://klunpa02.blogspot.com/"><i class="fa fa-fw fa-rss-square"></i>Blog</a></p>
	    </div>
	</div>
	<div class="row">
	    <div class="col-sm-4">
		    <ul class="img-list">
			  <li>
			    <img src="/assets/images/pages/team/bayle.jpg" width="100%" />
			    <span class="text-content"><span>
			    	<strong>Bayle Conrad</strong><br/>
			    	Master in Global Health from Emory University. Worked abroad in Uganda and Kenya, where she discovered a passion for community development and empowerment.  Now coordinates our globally dispersed team of Country Liaison interns.
			    </span></span>
			  </li>
			</ul>
	        <p><i class="fa fa-fw fa-user"></i><strong>Bayle Conrad</strong>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Liaison Coordinator</p>
	        <p><i class="fa fa-fw fa-map-marker"></i>Seattle, Washington&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://www.linkedin.com/profile/view?id=139129909"><i class="fa fa-fw fa-linkedin-square"></i>LinkedIn</a></p>
	    </div>
		<div class="col-sm-4">
		    <ul class="img-list">
			  <li>
			    <img src="/assets/images/pages/team/michaela.jpg" width="100%" />
			    <span class="text-content"><span>
			    	<strong>Michaela Ladstaetter</strong><br/>
			    	Holds degrees in business management and in political economy. Italian certified auditor and tax consultant. Speaks German, Italian and English. Arrived to Zidisha as a lender and felt that she would like to contribute more to the growth of this innovative model.
			    </span></span>
			  </li>
			</ul>
	        <p><i class="fa fa-fw fa-user"></i><strong>Michaela Ladstaetter</strong>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Accountant</p>
	        <p><i class="fa fa-fw fa-map-marker"></i>South Tyrol, Italy</p>
	    </div>		
	    <div class="col-sm-4">
		    <ul class="img-list">
			  <li>
			    <img src="/assets/images/pages/team/alissa.jpg" width="100%" />
			    <span class="text-content"><span>
			    	<strong>Alissa Jordan</strong><br/>
			    	Holds a B.A. in Communication Studies and is passionate about international nonprofit work. Has family from Kenya and looks forward to soon traveling to East Africa. Runner, writer, an avid bookworm, consumer of coffee and lover of new cultural experiences.
			    </span></span>
			  </li>
			</ul>
	        <p><i class="fa fa-fw fa-user"></i><strong>Alissa Jordan</strong>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Ghana Liaison</p>
	        <p><i class="fa fa-fw fa-map-marker"></i>Minneapolis, Minnesota</p>
	    </div>
	</div>
	<div class="row">
	    <div class="col-sm-4">
		    <ul class="img-list">
			  <li>
			    <img src="/assets/images/pages/team/mbemba.jpg" width="100%" />
			    <span class="text-content"><span>
			    	<strong>Mbemba Ousmane Kamara</strong><br/>
			    	Raised in Guinea in a hard-working entrepreneur family, as a child he spent many hours helping his parents produce traditional hand-dyed fabric. Now lives in London with his wife and children. Serves as first line of contact with our members in Guinea.
			    </span></span>
			  </li>
			</ul>
	        <p><i class="fa fa-fw fa-user"></i><strong>Mbemba Ousmane Kamara</strong>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Guinea Liaison</p>
	        <p><i class="fa fa-fw fa-map-marker"></i>London, United Kingdom</p>
	    </div>
	    <div class="col-sm-4">
		    <ul class="img-list">
			  <li>
			    <img src="/assets/images/pages/team/miriam.jpg" width="100%" />
			    <span class="text-content"><span>
			    	<strong>Miriam Frost</strong><br/>
			    	Recent graduate of the University of Rochester. Studied abroad in Senegal in 2012 and returned to try out life as an expat. Volunteers with refugees, tutors English, adopts stray dogs and cats, and is attempting to try every beignet lady in Dakar.
			    </span></span>
			  </li>
			</ul>
	        <p><i class="fa fa-fw fa-user"></i><strong>Miriam Frost</strong>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Senegal Ambassador</p>
	        <p><i class="fa fa-fw fa-map-marker"></i>Dakar, Senegal</p>
	    </div>
		<div class="col-sm-4">
		    <ul class="img-list">
			  <li>
			    <img src="/assets/images/pages/team/jonas.jpg" width="100%" />
			    <span class="text-content"><span>
			    	<strong>Jonas De Taeye</strong><br/>
			    	Has a master's degree in Mathematics and a passion for web development. First became involved with Zidisha as an open-source contributor. Enjoys playing music, jazz, cinema, reading, mountain biking, and independent study of mathematics and machine learning.
			    </span></span>
			  </li>
			</ul>
	        <p><i class="fa fa-fw fa-user"></i><strong>Jonas De Taeye</strong>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Web Developer</p>
	        <p><i class="fa fa-fw fa-map-marker"></i>Ghent, Belgium</p>
	    </div>
	</div>
	<div class="row" style="margin: 0 -20px 40px -20px !important;">
		<div class="col-sm-4">
		    <ul class="img-list">
			  <li>
			    <img src="/assets/images/pages/team/mamadou.jpg" width="100%" />
			    <span class="text-content"><span>
			    	<strong>Mamadou Gueye</strong><br/>
			    	Finance professional with master’s degree from HEC Montreal. After his first company failed, he discovered Zidisha which combines two of his main areas of interest: entrepreneurship and economic development in least developed countries.
			    </span></span>
			  </li>
			</ul>
	        <p><i class="fa fa-fw fa-user"></i><strong>Mamadou Gueye</strong>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Burkina Faso Liaison</p>
	        <p><i class="fa fa-fw fa-map-marker"></i>Montreal, Canada</p>
	    </div>
	    <div class="col-sm-4">
		    <ul class="img-list">
			  <li>
			    <img src="/assets/images/pages/team/taylor.png" width="100%" />
			    <span class="text-content"><span>
			    	<strong>Taylor Hanna</strong><br/>
			    	BA in Sociology.  Developed an interest in innovative development initiatives while working as an intern with Awamaki, a community development organization in Peru. Now works with Facebook’s ads integrity team and spends her spare time reading, running and eating tacos.
			    </span></span>
			  </li>
			</ul>
	        <p><i class="fa fa-fw fa-user"></i><strong>Taylor Hanna</strong>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Kenya Liaison</p>
	        <p><i class="fa fa-fw fa-map-marker"></i>Austin, Texas</p>
	    </div>
		<div class="col-sm-4">
		    <ul class="img-list">
			  <li>
			    <img src="/assets/images/pages/team/tom.png" width="100%" />
			    <span class="text-content"><span>
			    	<strong>Tom Skacel</strong><br/>
			    	B.Com in Finance from Sydney University. Passionate about social enterprise and has a strong interest in microfinance.  Prior trips to Africa inspired Tom to volunteer with Zidisha. Enjoys watching football, listening to classical music and playing poker.
			    </span></span>
			  </li>
			</ul>
	        <p><i class="fa fa-fw fa-user"></i><strong>Tom Skacel</strong>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Kenya Liaison</p>
	        <p><i class="fa fa-fw fa-map-marker"></i>Sydney, Australia</p>
	    </div>
	</div>
	<div class="row">
	    <div class="col-sm-4">
		    <ul class="img-list">
			  <li>
			    <img src="/assets/images/pages/team/kimberly.png" width="100%" />
			    <span class="text-content"><span>
			    	<strong>Kimberly Gollogly</strong><br/>
					Studying Commerce and Global Development at the University of Virginia.  Her passion for service and business originally began in high school when she worked with a social enterprise to create a customer loyalty plan.
				</span></span>
			  </li>
			</ul>
	        <p><i class="fa fa-fw fa-user"></i><strong>Kimberly Gollogly</strong>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Kenya Liaison</p>
	        <p><i class="fa fa-fw fa-map-marker"></i>Charlottesville, Virginia</p>
	    </div>
	    <div class="col-sm-4">
		    <ul class="img-list">
			  <li>
			    <img src="/assets/images/pages/team/caite.png" width="100%" />
			    <span class="text-content"><span>
			    	<strong>Caite Alexander</strong><br/>
			    	Studying Economics and East Asian Studies at Connecticut College. Watched her father go through the process of starting his own business as a child, and realized the importance of access to loans. Enjoys reading comics and learning new languages.
			    </span></span>
			  </li>
			</ul>
	        <p><i class="fa fa-fw fa-user"></i><strong>Caite Alexander</strong>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Kenya Liaison</p>
	        <p><i class="fa fa-fw fa-map-marker"></i>New London, Connecticut</p>
	    </div>
		<div class="col-sm-4">
		    <ul class="img-list">
			  <li>
			    <img src="/assets/images/pages/team/erin.png" width="100%" />
			    <span class="text-content"><span>
			    	<strong>Erin Eagan</strong><br/>
			    	Studies Economics at Kalamazoo College, writing thesis on microcredit in Senegal. Back in Dakar this summer after volunteering for Zidisha in Senegal last year.  Often finds herself teaching English to refugees in Michigan and children in Dakar schools.
			    </span></span>
			  </li>
			</ul>
	        <p><i class="fa fa-fw fa-user"></i><strong>Erin Eagan</strong>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Senegal Ambassador</p>
	        <p><i class="fa fa-fw fa-map-marker"></i>Dakar, Senegal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://www.linkedin.com/profile/view?id=182320699"><i class="fa fa-fw fa-linkedin-square"></i>LinkedIn</a></p>
	    </div>
	</div>
	<div class="row">
	    <div class="col-sm-4">
		    <ul class="img-list">
			  <li>
			    <img src="/assets/images/pages/team/lisbeth.jpg" width="100%" />
			    <span class="text-content"><span>
			    	<strong>Lisbeth Overheu</strong><br/>
			    	Comes from Perth in Western Australia. Sometimes works in a legal role in the Australian construction industry but avoids this as much as possible in favor of wandering the world with her backpack. Enjoys watching sport, reading trashy books and drinking wine.
			    </span></span>
			  </li>
			</ul>
	        <p><i class="fa fa-fw fa-user"></i><strong>Lisbeth Overheu</strong>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Kenya Ambassador</p>
	        <p><i class="fa fa-fw fa-map-marker"></i>Nairobi, Kenya</p>
	    </div>
	    <div class="col-sm-4">
		    <ul class="img-list">
			  <li>
			    <img src="/assets/images/pages/team/samantha.jpg" width="100%" />
			    <span class="text-content"><span>
			    	<strong>Samantha Bell</strong><br/>
			    	First experienced microfinance in Ecuador and was deeply impacted by the difference microloans make in the lives of the recipients. Likes wine and food, exploring places/culture/history, minimalist living, developing young leaders, yoga, and cycling.
			    </span></span>
			  </li>
			</ul>
	        <p><i class="fa fa-fw fa-user"></i><strong>Samantha Bell</strong>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Kenya Liaison</p>
	        <p><i class="fa fa-fw fa-map-marker"></i>San Francisco, California</p>
	    </div>
		<div class="col-sm-4">
		    <ul class="img-list">
			  <li>
			    <img src="/assets/images/pages/team/vikas.jpg" width="100%" />
			    <span class="text-content"><span>
			    	<strong>Vikas Lalwani</strong><br/>
			    	Grew up in a small town in Rajasthan, India. Left a top-notch job at Nestlé to pursue his zeal for entrepreneurship at a tech startup in Bangalore.  Holds degree in mechanical engineering.  Loves swimming, cricket and racing racecars he designs himself.
			    </span></span>
			  </li>
			</ul>
	        <p><i class="fa fa-fw fa-user"></i><strong>Vikas Lalwani</strong>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Community Manager</p>
	        <p><i class="fa fa-fw fa-map-marker"></i>Bangalore, India&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://www.linkedin.com/profile/view?id=274721265"><i class="fa fa-fw fa-linkedin-square"></i>LinkedIn</a></p>
	    </div>
	</div>
	<div class="row">
	    <div class="col-sm-4">
		    <ul class="img-list">
			  <li>
			    <img src="/assets/images/pages/team/lesley.jpg" width="100%" />
			    <span class="text-content"><span>
			    	<strong>Lesley De Dios</strong><br/>
			    	California native, has volunteered with microfinance efforts in the US, Cambodia, and the Philippines in a myriad of ways including community support, research, and marketing. Loves coaching gymnastics, traveling, and enjoying a good falafel.
			    </span></span>
			  </li>
			</ul>
	        <p><i class="fa fa-fw fa-user"></i><strong>Lesley De Dios</strong>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Community Manager</p>
	        <p><i class="fa fa-fw fa-map-marker"></i>Bangkok, Thailand&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://www.linkedin.com/profile/view?id=146830292"><i class="fa fa-fw fa-linkedin-square"></i>LinkedIn</a></p>
	    </div>
	    <div class="col-sm-4">
		    <ul class="img-list">
			  <li>
			    <img src="/assets/images/pages/team/kevin-o.png" width="100%" />
			    <span class="text-content"><span>
			    	<strong>Kevin O'Brien</strong><br/>
			    	Studying International Affairs and pursuing Army officer's commission at Northeastern University. Experienced economic empowerment firsthand through humanitarian work in Kenya. Loves to read classical fiction and collect historical autographs.
			    </span></span>
			  </li>
			</ul>
	        <p><i class="fa fa-fw fa-user"></i><strong>Kevin O'Brien</strong>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Kenya Liaison</p>
	        <p><i class="fa fa-fw fa-map-marker"></i>Boston, Massachusetts&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://www.linkedin.com/profile/view?id=190440938"><i class="fa fa-fw fa-linkedin-square"></i>LinkedIn</a></p>
	    </div>
	    <div class="col-sm-4">
		    <ul class="img-list">
			  <li>
			    <img src="/assets/images/pages/team/abi.jpg" width="100%" />
			    <span class="text-content"><span>
			    	<strong>Abi Ogunfowora</strong><br/>
			    	Works as an International Sales Consultant in Brussels, holds an Msc in E-Business from the University of London.  Extensive traveller, enjoys meeting and networking with different cultures.  Passionate about business in third-world economies.
			    </span></span>
			  </li>
			</ul>
	        <p><i class="fa fa-fw fa-user"></i><strong>Abi Ogunfowora</strong>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Kenya Liaison</p>
	        <p><i class="fa fa-fw fa-map-marker"></i>Brussels, Belgium</p>
	    </div>
	</div>
</div>

@stop