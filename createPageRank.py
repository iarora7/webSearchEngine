import networkx as nx
import operator
DG = nx.DiGraph()
#G.add_edge('A', 'B', weight=4)
#G.add_edge('B', 'D', weight=2)
#G.add_edge('A', 'C', weight=3)
#G.add_edge('C', 'D', weight=4)
#print nx.shortest_path(G, 'A', 'D', weight='weight')

fr = open("pagerank.csv", "r")
node_list = []

for line in fr:
	count = 0

	node = ""
	for word in line.split(','):
		if count == 0:
			node = word
			node_list.extend([node])
		else:
			DG.add_weighted_edges_from([(node.strip(),word.strip(),1.0)])
		count = count+1

pr = nx.pagerank(DG, alpha=0.9)
fr.close

#print node_list

op = open("pageRankFile.txt","w")
for key in pr:
	#print key,"=",pr(key)
	if key in node_list:
		op.write("/Users/isha/solr-5.3.1/crawl_data/"+str(key).replace("/", "@")+"@.html="+str(pr[key])+"\n")
op.close
